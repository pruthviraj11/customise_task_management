<?php

namespace App\Http\Controllers;

use App\Http\Requests\Priority\CreatePriorityRequest;
use App\Http\Requests\Priority\UpdatePriorityRequest;
use App\Models\Priority;


use App\Services\RoleService;
use App\Services\PriorityService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class PriorityController extends Controller
{
    protected PriorityService $priorityService;

    public function __construct(PriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
        // $this->roleService = $roleService;
        $this->middleware('permission:priority-list|priority-create|priority-edit|priority-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:priority-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:priority-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:priority-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'priority-list', 'guard_name' => 'web', 'module_name' => 'Priority']);
        // Permission::create(['name' => 'priority-create', 'guard_name' => 'web', 'module_name' => 'Priority']);
        // Permission::create(['name' => 'priority-edit', 'guard_name' => 'web', 'module_name' => 'Priority']);
        // Permission::create(['name' => 'priority-delete', 'guard_name' => 'web', 'module_name' => 'Priority']);

    }


    public function index()
    {
        $data['total_department'] = Priority::count();
        $data['department'] = Priority::get();
        // dd($data);


        return view('content.apps.priority.list', compact('data'));
    }

    public function getAll()
    {
        $priority = $this->priorityService->getAllpriority();

        return DataTables::of($priority)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-priority-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos=' $encryptedId' id='confirm-color' href='" . route('app-priority-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->rawColumns(['actions'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Priority";
        $page_data['form_title'] = "Add New Priority";
        $department = '';
        $departmentslist = $this->priorityService->getAllpriority();
        $data['department'] = Priority::all();
        // $data['parent'] = Priority::with('parent')->whereNull('parent_id')->get();
        // $selectedparentDepartment = '';
        return view('.content.apps.priority.create-edit', compact('page_data', 'department', 'departmentslist', 'data'));
    }

    public function store(CreatePriorityRequest $request)
    {
        // dd($request->all());
        try {

            $PriorityData['displayname'] = $request->get('displayname');
            $PriorityData['priority_name'] = $request->get('priority_name');
            $PriorityData['created_by'] = auth()->user()->id;
            $PriorityData['status'] = $request->get('status');
            $department = $this->priorityService->create($PriorityData);


            if (!empty($department)) {
                return redirect()->route("app-priority-list")->with('success', 'Priority Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Priority');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-priority-list")->with('error', 'Error while adding Priority');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $department = $this->priorityService->getpriority($id);
            $page_data['page_title'] = "Priority";
            $page_data['form_title'] = "Edit Priority";

            $departmentslist = $this->priorityService->getAllpriority();
            $data['department'] = Priority::all();


            return view('.content.apps.priority.create-edit', compact('page_data', 'department', 'data', 'departmentslist'));
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-priority-list")->with('error', 'Error while editing Priority');
        }
    }

    public function update(UpdatePriorityRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $PriorityData['displayname'] = $request->get('displayname');
            $PriorityData['priority_name'] = $request->get('priority_name');
            $PriorityData['updated_by'] = auth()->user()->id;
            $PriorityData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->priorityService->updatepriority($id, $PriorityData);


            //            $role = Role::find($request->get('role'));
//            $updated->assignRole($role);
            if (!empty($updated)) {
                return redirect()->route("app-priority-list")->with('success', 'Priority Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Priority');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-priority-list")->with('error', 'Error while editing Priority');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->priorityService->deletepriority($id);
            if (!empty($deleted)) {
                return redirect()->route("app-priority-list")->with('success', 'Priority Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Priority');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-priority-list")->with('error', 'Error while editing Priority');
        }
    }
}
