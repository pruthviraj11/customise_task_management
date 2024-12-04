<?php

namespace App\Http\Controllers;

use App\Http\Requests\Status\CreateStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use App\Models\Status;


use App\Services\RoleService;
use App\Services\StatusService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class StatusController extends Controller
{
    protected StatusService $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
        // $this->roleService = $roleService;
        $this->middleware('permission:status-list|status-create|status-edit|status-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:status-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:status-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:status-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'status-list', 'guard_name' => 'web', 'module_name' => 'Status']);
        // Permission::create(['name' => 'status-create', 'guard_name' => 'web', 'module_name' => 'Status']);
        // Permission::create(['name' => 'status-edit', 'guard_name' => 'web', 'module_name' => 'Status']);
        // Permission::create(['name' => 'status-delete', 'guard_name' => 'web', 'module_name' => 'Status']);

    }


    public function index()
    {
        $data['total_department'] = Status::count();
        $data['department'] = Status::get();
        // dd($data);


        return view('content.apps.status.list', compact('data'));
    }

    public function getAll()
    {
        $departments = $this->statusService->getAllstatus();

        return DataTables::of($departments)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-status-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-status-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->rawColumns(['actions'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Status";
        $page_data['form_title'] = "Add New Status";
        $department = '';
        $departmentslist = $this->statusService->getAllstatus();
        $data['department'] = Status::all();
        // $data['parent'] = Status::with('parent')->whereNull('parent_id')->get();
        // $selectedparentDepartment = '';
        return view('.content.apps.status.create-edit', compact('page_data', 'department', 'departmentslist', 'data'));
    }

    public function store(CreateStatusRequest $request)
    {
        // dd($request->all());
        try {

            $statusData['displayname'] = $request->get('displayname');
            $statusData['status_name'] = $request->get('status_name');
            $statusData['created_by'] = auth()->user()->id;
            $statusData['status'] = $request->get('status');
            $status = $this->statusService->create($statusData);


            if (!empty($status)) {
                return redirect()->route("app-status-list")->with('success', 'Status Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Status');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-status-list")->with('error', 'Error while adding Status');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $department = $this->statusService->getstatus($id);
            $page_data['page_title'] = "Status";
            $page_data['form_title'] = "Edit Status";

            $departmentslist = $this->statusService->getAllstatus();
            $data['department'] = Status::all();


            return view('.content.apps.status.create-edit', compact('page_data', 'department', 'data', 'departmentslist'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-status-list")->with('error', 'Error while editing Status');
        }
    }

    public function update(UpdateStatusRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $statusData['displayname'] = $request->get('displayname');
            $statusData['status_name'] = $request->get('status_name');
            $statusData['updated_by'] = auth()->user()->id;
            $statusData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->statusService->updatestatus($id, $statusData);


            //            $role = Role::find($request->get('role'));
//            $updated->assignRole($role);
            if (!empty($updated)) {
                return redirect()->route("app-status-list")->with('success', 'Status Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Status');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-status-list")->with('error', 'Error while editing Status');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->statusService->deletestatus($id);
            if (!empty($deleted)) {
                return redirect()->route("app-status-list")->with('success', 'Status Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Status');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-status-list")->with('error', 'Error while editing Status');
        }
    }
}
