<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DepartmentsImport;

use App\Services\RoleService;
use App\Services\DepartmentService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
        // $this->roleService = $roleService;
        $this->middleware('permission:department-list|department-create|department-edit|department-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:department-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:department-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:department-delete', ['only' => ['destroy']]);

        //  Permission::create(['name' => 'department-list', 'guard_name' => 'web', 'module_name' => 'Department']);
        //  Permission::create(['name' => 'department-create', 'guard_name' => 'web', 'module_name' => 'Department']);
        //  Permission::create(['name' => 'department-edit', 'guard_name' => 'web', 'module_name' => 'Department']);
        //  Permission::create(['name' => 'department-delete', 'guard_name' => 'web', 'module_name' => 'Department']);

    }


    public function index()
    {
        $data['total_department'] = Department::count();
        $data['department'] = Department::get();

        return view('content.apps.department.list', compact('data'));
    }

    public function getAll()
    {
        $departments = $this->departmentService->getAllDepartment();

        return DataTables::of($departments)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-department-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-department-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->addColumn('hod_username', function ($row) {
            if ($row->user) {
                return $row->user->first_name . ' ' . $row->user->last_name;
            } else {
                return "-";
            }

        })->rawColumns(['actions', 'hod_username'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Department";
        $page_data['form_title'] = "Add New Department";
        $department = '';
        $users = User::where('status', '1')->get();
        $departmentslist = $this->departmentService->getAllDepartment();
        $data['department'] = Department::all();
        return view('.content.apps.department.create-edit', compact('page_data', 'department', 'departmentslist', 'data', 'users'));
    }

    public function store(CreateDepartmentRequest $request)
    {
        try {

            $departmentData['description'] = $request->get('description');
            $departmentData['department_name'] = $request->get('department_name');
            $departmentData['hod'] = $request->get('hod');
            $departmentData['created_by'] = auth()->user()->id;
            $departmentData['status'] = $request->get('status');
            $department = $this->departmentService->create($departmentData);


            if (!empty($department)) {
                return redirect()->route("app-department-list")->with('success', 'Department Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Department');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-department-list")->with('error', 'Error while adding Department');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $department = $this->departmentService->getDepartment($id);
            $page_data['page_title'] = "Department";
            $page_data['form_title'] = "Edit Department";
            $users = User::where('status', '1')->get();
            $departmentslist = $this->departmentService->getAllDepartment();
            $data['department'] = Department::all();


            return view('.content.apps.department.create-edit', compact('page_data', 'department', 'data', 'departmentslist', 'users'));
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-department-list")->with('error', 'Error while editing Department');
        }
    }

    public function update(UpdateDepartmentRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $departmentData['description'] = $request->get('description');
            $departmentData['hod'] = $request->get('hod');
            $departmentData['department_name'] = $request->get('department_name');
            $departmentData['updated_by'] = auth()->user()->id;
            $departmentData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->departmentService->updateDepartment($id, $departmentData);


            if (!empty($updated)) {
                return redirect()->route("app-department-list")->with('success', 'Department Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Department');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-department-list")->with('error', 'Error while editing Department');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->departmentService->deleteDepartment($id);
            if (!empty($deleted)) {
                return redirect()->route("app-department-list")->with('success', 'Departments Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Departments');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-department-list")->with('error', 'Error while editing Departments');
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new DepartmentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Departments Imported Successfully!');
    }
}
