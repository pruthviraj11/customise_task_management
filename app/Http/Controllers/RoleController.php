<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Spatie\Permission\Models\Permission;
use DataTables;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        //        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'show']]);
//        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
//
//         Permission::create(['name' => 'role-list', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-create', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-edit', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-delete', 'guard_name' => 'web', 'module_name' => 'Role']);


        //         Permission::create(['name' => 'user-list', 'guard_name' => 'web', 'module_name' => 'User']);
//         Permission::create(['name' => 'user-create', 'guard_name' => 'web', 'module_name' => 'User']);
//         Permission::create(['name' => 'user-edit', 'guard_name' => 'web', 'module_name' => 'User']);
//         Permission::create(['name' => 'user-delete', 'guard_name' => 'web', 'module_name' => 'User']);
//          Permission::create(['name' => 'department-list', 'guard_name' => 'web', 'module_name' => 'Department']);
//          Permission::create(['name' => 'department-create', 'guard_name' => 'web', 'module_name' => 'Department']);
//          Permission::create(['name' => 'department-edit', 'guard_name' => 'web', 'module_name' => 'Department']);
//          Permission::create(['name' => 'department-delete', 'guard_name' => 'web', 'module_name' => 'Department']);
//         Permission::create(['name' => 'priority-list', 'guard_name' => 'web', 'module_name' => 'Priority']);
//         Permission::create(['name' => 'priority-create', 'guard_name' => 'web', 'module_name' => 'Priority']);
//         Permission::create(['name' => 'priority-edit', 'guard_name' => 'web', 'module_name' => 'Priority']);
//         Permission::create(['name' => 'priority-delete', 'guard_name' => 'web', 'module_name' => 'Priority']);
//         Permission::create(['name' => 'project-list', 'guard_name' => 'web', 'module_name' => 'Project']);
//         Permission::create(['name' => 'project-create', 'guard_name' => 'web', 'module_name' => 'Project']);
//         Permission::create(['name' => 'project-edit', 'guard_name' => 'web', 'module_name' => 'Project']);
//         Permission::create(['name' => 'project-delete', 'guard_name' => 'web', 'module_name' => 'Project']);
//         Permission::create(['name' => 'project-status-list', 'guard_name' => 'web', 'module_name' => 'Project Status']);
//         Permission::create(['name' => 'project-status-create', 'guard_name' => 'web', 'module_name' => 'Project Status']);
//         Permission::create(['name' => 'project-status-edit', 'guard_name' => 'web', 'module_name' => 'Project Status']);
//         Permission::create(['name' => 'project-status-delete', 'guard_name' => 'web', 'module_name' => 'Project Status']);
//         Permission::create(['name' => 'status-list', 'guard_name' => 'web', 'module_name' => 'Status']);
//         Permission::create(['name' => 'status-create', 'guard_name' => 'web', 'module_name' => 'Status']);
//         Permission::create(['name' => 'status-edit', 'guard_name' => 'web', 'module_name' => 'Status']);
//         Permission::create(['name' => 'status-delete', 'guard_name' => 'web', 'module_name' => 'Status']);
//          Permission::create(['name' => 'sub_department-list', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
//          Permission::create(['name' => 'sub_department-create', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
//          Permission::create(['name' => 'sub_department-edit', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
//          Permission::create(['name' => 'sub_department-delete', 'guard_name' => 'web', 'module_name' => 'SubDepartment']);
//         Permission::create(['name' => 'task-list', 'guard_name' => 'web', 'module_name' => 'Task']);
//         Permission::create(['name' => 'task-create', 'guard_name' => 'web', 'module_name' => 'Task']);
//         Permission::create(['name' => 'task-edit', 'guard_name' => 'web', 'module_name' => 'Task']);
//         Permission::create(['name' => 'task-delete', 'guard_name' => 'web', 'module_name' => 'Task']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('/content/apps/roles/list');
    }

    public function permissions_list()
    {
        $permissions = Permission::get();
        $groupedPermissions = $permissions->groupBy('module_name');
        $groupedPermissionsWithAllData = $groupedPermissions->map(function ($group) {
            return $group->all();
        });
        return $groupedPermissionsWithAllData;
    }
    public function getAll()
    {
        $roles = $this->roleService->getAllRoles();
        return DataTables::of($roles)
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a data-bs-toggle='tooltip' title='Edit' data-bs-delay='400' class='btn btn-warning'  href='" . route('app-roles-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

                // Delete Button
                $deleteButton = "<a data-bs-toggle='tooltip' title='Delete' data-bs-delay='400' class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-roles-delete', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                return $updateButton . " " . $deleteButton;
            })->rawColumns(['actions'])->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = "";
        $page_data['page_title'] = "Role Add";
        $page_data['form_title'] = "Add New Role";
        $permissions = Permission::get();
        $groupedPermissions = $permissions->groupBy('module_name');
        $groupedPermissionsWithAllData = $groupedPermissions->map(function ($group) {
            return $group->all();
        });
        return view('/content/apps/roles/create-edit', compact('page_data', 'role', 'groupedPermissionsWithAllData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        try {
            $roleData['name'] = $request->name;
            $roleData['display_name'] = $request->display_name;
            $permissions = $request->input('permissions');
            $role = $this->roleService->create($roleData, $permissions);
            if (!empty($role)) {
                return redirect()->route("app-roles-list")->with('success', 'Role Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Role');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-roles-list")->with('error', 'Error while editing Role');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $role = $this->roleService->getRole($id);
            $page_data['page_title'] = "Role Edit";
            $page_data['form_title'] = "Edit Role";
            $permissions = Permission::get();
            $groupedPermissions = $permissions->groupBy('module_name');
            $groupedPermissionsWithAllData = $groupedPermissions->map(function ($group) {
                return $group->all();
            });
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            return view('/content/apps/roles/create-edit', compact('page_data', 'role', 'groupedPermissionsWithAllData', 'rolePermissions'));
        } catch (\Exception $error) {
            return redirect()->route("app-roles-list")->with('error', 'Error while editing Role');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $roleData['name'] = $request->name;
            $roleData['display_name'] = $request->display_name;
            $permissions = $request->input('permissions');
            $updated = $this->roleService->updateRole($id, $roleData, $permissions);
            if (!empty($updated)) {
                return redirect()->route("app-roles-list")->with('success', 'Role Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Role');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-roles-list")->with('error', 'Error while editing Role');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->roleService->deleteRole($id);
            if (!empty($deleted)) {
                return redirect()->route("app-roles-list")->with('success', 'Role Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Role');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-roles-list")->with('error', 'Error while editing Role');
        }
    }
}
