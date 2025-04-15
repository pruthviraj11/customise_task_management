<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubDepartment\CreateSubDepartmentRequest;
use App\Http\Requests\SubDepartment\UpdateSubDepartmentRequest;
use App\Models\SubDepartment;
use App\Models\Department;


use App\Services\RoleService;
use App\Services\SubDepartmentService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class SubDepartmentController extends Controller
{
    protected SubDepartmentService $sub_departmentService;

    public function __construct(SubDepartmentService $sub_departmentService)
    {
        $this->sub_departmentService = $sub_departmentService;
        // $this->roleService = $roleService;
        $this->middleware('permission:sub_department-list|sub_department-create|sub_department-edit|sub_department-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:sub_department-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sub_department-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sub_department-delete', ['only' => ['destroy']]);

        //  Permission::create(['name' => 'sub_department-list', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
        //  Permission::create(['name' => 'sub_department-create', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
        //  Permission::create(['name' => 'sub_department-edit', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
        //  Permission::create(['name' => 'sub_department-delete', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);

    }


    public function index()
    {
        $data['total_sub_department'] = SubDepartment::count();
        $data['sub_department'] = SubDepartment::get();

        return view('content.apps.sub_department.list', compact('data'));
    }

    public function getAll()
    {
        $sub_departments = $this->sub_departmentService->getAllSubDepartment()->with('department')->get();
        // dd($sub_departments-);
        return DataTables::of($sub_departments)->addColumn('actions', function ($row) {
            // dd($row->department->department_name);
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-sub_department-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-sub_department-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })
            ->addColumn('department', function ($row) {
                if ($row->department->department_name) {
                    return $row->department->department_name;
                } else {
                    return " - ";
                }
            })

            ->rawColumns(['actions', 'department'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "SubDepartment";
        $page_data['form_title'] = "Add New Sub Department";
        $sub_department = '';
        $departments = Department::where('status', 'on')->get();
        $sub_departmentslist = $this->sub_departmentService->getAllSubDepartment();
        $data['sub_department'] = SubDepartment::all();
        return view('.content.apps.sub_department.create-edit', compact('page_data', 'sub_department', 'departments', 'sub_departmentslist', 'data'));
    }

    public function store(CreateSubDepartmentRequest $request)
    {
        try {
            $sub_departmentData['description'] = $request->get('description');
            $sub_departmentData['sub_department_name'] = $request->get('sub_department_name');
            $sub_departmentData['created_by'] = auth()->user()->id;
            $sub_departmentData['status'] = $request->get('status');
            $sub_departmentData['department_id'] = $request->get('department_id');

            $sub_department = $this->sub_departmentService->create($sub_departmentData);


            if (!empty($sub_department)) {
                return redirect()->route("app-sub_department-list")->with('success', 'SubDepartment Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding SubDepartment');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-sub_department-list")->with('error', 'Error while adding SubDepartment');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $sub_department = $this->sub_departmentService->getSubDepartment($id);
            $page_data['page_title'] = "SubDepartment";
            $page_data['form_title'] = "Edit SubDepartment";

            $sub_departmentslist = $this->sub_departmentService->getAllSubDepartment();
            $data['sub_department'] = SubDepartment::all();
            $departments = Department::where('status', 'on')->get();


            return view('.content.apps.sub_department.create-edit', compact('page_data', 'departments', 'sub_department', 'data', 'sub_departmentslist'));
        } catch (\Exception $error) {
            return redirect()->route("app-sub_department-list")->with('error', 'Error while editing SubDepartment');
        }
    }

    public function update(UpdateSubDepartmentRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $sub_departmentData['description'] = $request->get('description');
            $sub_departmentData['sub_department_name'] = $request->get('sub_department_name');
            $sub_departmentData['department_id'] = $request->get('department_id');
            $sub_departmentData['updated_by'] = auth()->user()->id;
            $sub_departmentData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->sub_departmentService->updateSubDepartment($id, $sub_departmentData);


            if (!empty($updated)) {
                return redirect()->route("app-sub_department-list")->with('success', 'SubDepartment Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating SubDepartment');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-sub_department-list")->with('error', 'Error while editing SubDepartment');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $sub_departmentData['deleted_by'] = Auth()->user()->id;
            $updated = $this->sub_departmentService->updateSubDepartment($id, $sub_departmentData);
            $deleted = $this->sub_departmentService->deleteSubDepartment($id);
            if (!empty($deleted)) {
                return redirect()->route("app-sub_department-list")->with('success', 'SubDepartments Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting SubDepartments');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-sub_department-list")->with('error', 'Error while editing SubDepartments');
        }
    }
}
