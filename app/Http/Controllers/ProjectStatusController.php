<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStatus\CreateProjectStatusRequest;
use App\Http\Requests\ProjectStatus\UpdateProjectStatusRequest;
use App\Models\ProjectStatus;


use App\Services\RoleService;
use App\Services\ProjectStatusService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class ProjectStatusController extends Controller
{
    protected ProjectStatusService $projectStatusService;

    public function __construct(ProjectStatusService $projectStatusService)
    {
        $this->projectStatusService = $projectStatusService;
        // $this->roleService = $roleService;
        $this->middleware('permission:project-status-list|project-status-create|project-status-edit|project-status-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:project-status-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:project-status-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:project-status-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'project-status-list', 'guard_name' => 'web', 'module_name' => 'Project Status']);
        // Permission::create(['name' => 'project-status-create', 'guard_name' => 'web', 'module_name' => 'Project Status']);
        // Permission::create(['name' => 'project-status-edit', 'guard_name' => 'web', 'module_name' => 'Project Status']);
        // Permission::create(['name' => 'project-status-delete', 'guard_name' => 'web', 'module_name' => 'Project Status']);

    }


    public function index()
    {
        $data['total_department'] = ProjectStatus::count();
        $data['department'] = ProjectStatus::get();
        // dd($data);


        return view('content.apps.ProjectStatus.list', compact('data'));
    }

    public function getAll()
    {
        $departments = $this->projectStatusService->getAllProjectStatus();

        return DataTables::of($departments)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-project-status-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-project-status-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->rawColumns(['actions'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Project Status";
        $page_data['form_title'] = "Add New Project Status";
        $department = '';
        $departmentslist = $this->projectStatusService->getAllProjectStatus();
        $data['department'] = ProjectStatus::all();
        // $data['parent'] = ProjectStatus::with('parent')->whereNull('parent_id')->get();
        // $selectedparentDepartment = '';
        return view('.content.apps.ProjectStatus.create-edit', compact('page_data', 'department', 'departmentslist', 'data'));
    }

    public function store(CreateProjectStatusRequest $request)
    {
        // dd($request->all());
        try {

            $departmentData['displayname'] = $request->get('displayname');
            $departmentData['project_status_name'] = $request->get('project_status_name');
            $departmentData['created_by'] = auth()->user()->id;
            $departmentData['status'] = $request->get('status');
            $department = $this->projectStatusService->create($departmentData);


            if (!empty($department)) {
                return redirect()->route("app-project-status-list")->with('success', 'Project Status Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Project Status');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-project-status-list")->with('error', 'Error while adding Project Status');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $department = $this->projectStatusService->getProjectStatus($id);
            $page_data['page_title'] = "Project Status";
            $page_data['form_title'] = "Edit Project Status";

            $departmentslist = $this->projectStatusService->getAllProjectStatus();
            $data['department'] = ProjectStatus::all();


            return view('.content.apps.ProjectStatus.create-edit', compact('page_data', 'department', 'data', 'departmentslist'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-project-status-list")->with('error', 'Error while editing Project Status');
        }
    }

    public function update(UpdateProjectStatusRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $departmentData['displayname'] = $request->get('displayname');
            $departmentData['project_status_name'] = $request->get('project_status_name');
            $departmentData['updated_by'] = auth()->user()->id;
            $departmentData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->projectStatusService->updateProjectStatus($id, $departmentData);


            //            $role = Role::find($request->get('role'));
//            $updated->assignRole($role);
            if (!empty($updated)) {
                return redirect()->route("app-project-status-list")->with('success', 'Project Status Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Project Status');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-project-status-list")->with('error', 'Error while editing Project Status');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->projectStatusService->deleteProjectStatus($id);
            if (!empty($deleted)) {
                return redirect()->route("app-project-status-list")->with('success', 'Project Status Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Project Status');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-project-status-list")->with('error', 'Error while editing Project Status');
        }
    }
}

