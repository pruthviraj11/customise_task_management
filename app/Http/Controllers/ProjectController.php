<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\UserProject;


use App\Services\RoleService;
use App\Services\ProjectService;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
        // $this->roleService = $roleService;
        $this->middleware('permission:project-list|project-create|project-edit|project-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:project-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:project-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:project-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'project-list', 'guard_name' => 'web', 'module_name' => 'Project']);
        // Permission::create(['name' => 'project-create', 'guard_name' => 'web', 'module_name' => 'Project']);
        // Permission::create(['name' => 'project-edit', 'guard_name' => 'web', 'module_name' => 'Project']);
        // Permission::create(['name' => 'project-delete', 'guard_name' => 'web', 'module_name' => 'Project']);

    }


    public function index()
    {
        $data['total_project'] = Project::count();
        $data['project'] = Project::get();

        return view('content.apps.project.list', compact('data'));
    }

    public function getAll()
    {
        $projects = $this->projectService->getAllProject();

        return DataTables::of($projects)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-project-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-project-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->addColumn('users', function ($row) {
            return $row->users->pluck('first_name')->implode(', ');
        })
            ->rawColumns(['actions', 'users'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Project";
        $page_data['form_title'] = "Add New Project";
        $project = '';
        $users = User::all();
        $projectStatuses = ProjectStatus::all();
        $projectslist = $this->projectService->getAllProject();
        $data['project'] = Project::all();
        return view('.content.apps.project.create-edit', compact('page_data', 'projectStatuses', 'project', 'users', 'projectslist', 'data'));
    }

    public function store(CreateProjectRequest $request)
    {
        try {

            $projectData['description'] = $request->get('description');
            $projectData['prifix'] = $request->get('prifix');
            $projectData['color'] = $request->get('color');
            $projectData['project_name'] = $request->get('project_name');
            $projectData['project_status_id'] = $request->get('project_status_id');
            $projectData['created_by'] = auth()->user()->id;
            $projectData['status'] = $request->get('status');

            $project = $this->projectService->create($projectData);
            $userIds = $request->input('user_id', []);
            $project->users()->sync($userIds);

            if (!empty($project)) {
                return redirect()->route("app-project-list")->with('success', 'Project Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Project');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-project-list")->with('error', 'Error while adding Project');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $users = User::all();
            $projectStatuses = ProjectStatus::all();
            $id = decrypt($encrypted_id);
            $project = $this->projectService->getProject($id);
            $project->user_ids = $project->users->pluck('id')->toArray();
            $page_data['page_title'] = "Project";
            $page_data['form_title'] = "Edit Project";


            $projectslist = $this->projectService->getAllProject();
            $data['project'] = Project::all();
            $departments = User::where('status', 'on')->get();


            return view('.content.apps.project.create-edit', compact('page_data', 'departments', 'users', 'projectStatuses', 'project', 'data', 'projectslist'));
        } catch (\Exception $error) {
            return redirect()->route("app-project-list")->with('error', 'Error while editing Project');
        }
    }

    public function update(UpdateProjectRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $projectData['description'] = $request->get('description');
            $projectData['prifix'] = $request->get('prifix');
            $projectData['color'] = $request->get('color');
            $projectData['project_name'] = $request->get('project_name');
            $projectData['project_status_id'] = $request->get('project_status_id');
            $projectData['created_by'] = auth()->user()->id;
            $projectData['status'] = $request->get('status');
            $updated = $this->projectService->updateProject($id, $projectData);
            $userIds = $request->input('user_id', []);

            if ($updated) {
                $project = Project::find($id);
                $userIds = $request->input('user_id', []);
                $previousUserIds = $project->users->pluck('id')->toArray();
                $removedUserIds = array_diff($previousUserIds, $userIds);
                if (!empty($removedUserIds)) {
                    $project->users()->detach($removedUserIds);
                }
                $newUserIds = array_diff($userIds, $previousUserIds);
                if (!empty($newUserIds)) {
                    $project->users()->attach($newUserIds);
                }
                return redirect()->route("app-project-list")->with('success', 'Project Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Project');
            }

            if (!empty($updated)) {
                return redirect()->route("app-project-list")->with('success', 'Project Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Project');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-project-list")->with('error', 'Error while editing Project');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->projectService->deleteProject($id);
            if (!empty($deleted)) {
                return redirect()->route("app-project-list")->with('success', 'Projects Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Projects');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-project-list")->with('error', 'Error while editing Projects');
        }
    }
}
