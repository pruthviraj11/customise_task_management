<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;

use App\Models\Location;
use App\Models\Role;
use App\Models\SubDepartment;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use App\Mail\TaskCreatedMail;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Support\Facades\Mail;
// use App\Http\Controllers\View;
use View;
use Carbon\Carbon;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    protected UserService $userService;
    protected RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);

        //         Permission::create(['name' => 'user-list', 'guard_name' => 'web', 'module_name' => 'user']);
//         Permission::create(['name' => 'user-create', 'guard_name' => 'web', 'module_name' => 'user']);
//         Permission::create(['name' => 'user-edit', 'guard_name' => 'web', 'module_name' => 'user']);
//         Permission::create(['name' => 'user-delete', 'guard_name' => 'web', 'module_name' => 'user']);

        //         Permission::create(['name' => 'role-list', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-create', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-edit', 'guard_name' => 'web', 'module_name' => 'Role']);
//         Permission::create(['name' => 'role-delete', 'guard_name' => 'web', 'module_name' => 'Role']);
//
//
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        //        assignSameRole();
//        userDataUpdate();
//        giveAllPermissionToRole();
//        roleDataImport();
//        userDataImport();
//        assignRole();
//        departmentDataImport();
//        subDepartmentDataImport();
//        statusImport();
//        projectStatusImport();
//        projectImport();
//        projectUsersImport();
//        taskImport();
//        taskAssigneesImport();
        $data['total_user'] = User::where('status', true)->count();
        $data['admin_count'] = User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')->where('roles.display_name', 'Admin')->count();

        return view('content.apps.user.list', compact('data'));
    }


    public function getAll(Request $request)
    {
        // $users = $this->userService->getAllUser();
        $users = User::query();
        // dd($users->username());
        // $users = $this->userService->getAllUser();

        // if (!empty($request->search['value'])) {
        //     // $tasks = Task::query();
        //     $searchTerm = $request->search['value'];
        //     $users->where(function ($query) use ($searchTerm) {
        //         $query->where('username', 'like', '%' . $searchTerm . '%');
        //         // ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
        //         // ->orWhere('title', 'like', '%' . $searchTerm . '%');
        //         // Add other columns as needed
        //     });
        // }
        return DataTables::of($users)
            ->orderColumn('full_name', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
            })
            ->addColumn('full_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })->addColumn('role_name', function ($row) {
                return head($row->getRoleNames());
            })->addColumn('department', function ($row) {
                return $row->department->department_name ?? '-';
            })->addColumn('subdepartment', function ($row) {
                return $row->sub_department->sub_department_name ?? '-';
            })->addColumn('phone_no', function ($row) {
                return $row->phone_no;
            })->addColumn('username', function ($row) {
                return $row->username;
            })->addColumn('dob', function ($row) {
                // dd($row->dob);
                if ($row->dob != null) {
                    return Carbon::parse($row->dob)->format('d-m-Y');
                }


                return '-';
            })->addColumn('address', function ($row) {
                return $row->address;
            })->addColumn('report_to', function ($row) {
                $row->report_to;
                $report_to = User::where('id', $row->report_to)->first();
                if ($report_to) {
                    return $report_to->first_name . ' ' . $report_to->last_name;
                }
                return '-';
            })->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a data-bs-toggle='tooltip' title='Edit' data-bs-delay='400' class='mb-1 btn btn-warning'  href='" . route('app-users-edit', $encryptedId) . "'><i data-feather='edit'></i></a>";

                // Delete Button
                $deleteButton = "<a data-bs-toggle='tooltip' title='Delete' data-bs-delay='400' class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color  href='" . route('app-users-destroy', $encryptedId) . "'><i data-feather='trash-2'></i></a>";

                return $updateButton . " " . $deleteButton;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $page_data['page_title'] = "User";
        $page_data['form_title'] = "Add New User";
        $user = '';
        $departments = Department::where('status', 'on')->get();
        $Subdepartments = SubDepartment::where('status', 'on')->get();
        $profile_img = '';
        $userslist = $this->userService->getAllUser();
        $roles = $this->roleService->getAllRoles();
        $locations = Location::where('status', 'on')->get();
        $data['reports_to'] = User::all();
        return view('.content.apps.user.create-edit', compact('page_data', 'user', 'userslist', 'roles', 'data', 'profile_img', 'departments', 'Subdepartments','locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        // dd($request->all());
        try {
            $userData['username'] = $request->get('username');
            $userData['first_name'] = $request->get('first_name');
            $userData['last_name'] = $request->get('last_name');
            $userData['email'] = $request->get('email');
            $userData['phone_no'] = $request->get('phone_no');
            $userData['password'] = Hash::make($request->get('password'));
            $userData['subdepartment'] = $request->get('subdepartment');
            $userData['department_id'] = $request->get('department_id');
            $userData['location_id'] = $request->get('location_id');

            $userData['designation'] = $request->get('designation');
            $userData['G7'] = $request->get('G7');
            $userData['dob'] = $request->get('dob');
            $userData['address'] = $request->get('address');
            $userData['branch'] = $request->get('branch');
            $userData['form_group'] = $request->get('form_group');
            $userData['report_to'] = $request->get('report_to');
            $userData['authorization'] = $request->get('authorization');
            $userData['can_export_excel'] = $request->get('can_export_excel') == 'on' ? true : false;
            $userData['can_print_reports'] = $request->get('can_print_reports') == 'on' ? true : false;
            $userData['can_remove_tax'] = $request->get('can_remove_tax') == 'on' ? true : false;
            $userData['can_delete_package'] = $request->get('can_delete_package') == 'on' ? true : false;
            $userData['status'] = $request->get('status') == 'on' ? true : false;
            $userData['created_by'] = Auth::id();

            if ($request->hasFile('profile_img')) {
                $imagePath = $request->file('profile_img')->store('profile_img', 'public');
                $userData['profile_img'] = $imagePath;
            }

            $user = $this->userService->create($userData);
            // $role = Role::find($request->get('role'));
            // $user->assignRole($role);
            // dd($user);
            $user_data = User::where('email', $user->email)->first();
            // dd($user_data);
            $role = Role::find($request->get('role'));
            $user_data->syncRoles([]);
            $user_data->assignRole($role);
            $userData['passwordView'] = ($request->get('password'));
            $UserEmail = $request->email;
            $subject = "New User Created";
            $html = View::make('emails.user_created', compact('userData'))->render();
            // dd($html);
            // Mail::to($UserEmail)->send(new TaskCreatedMail($subject, $UserEmail));


            if (!empty($user)) {
                return redirect()->route("app-users-list")->with('success', 'User Added Successfully');
            } else {
                return redirect()->back()->with('success', 'User Added Successfully');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'User Added Successfully');
        }
    }

    public function profile($encrypted_id)
    {
        $id = decrypt($encrypted_id);

        $data = User::find($id);
        return view('.content.pages.page-account-settings-account', compact('data'));
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $user = $this->userService->getUser($id);
            $page_data['page_title'] = "User";
            $page_data['form_title'] = "Edit User";
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $userslist = $this->userService->getAllUser();
            $roles = $this->roleService->getAllRoles();
            $user->role = $user->getRoleNames()[0] ?? '';
            $associatedSubDepartmentId = $user->subDepartment->id ?? null;
            // dd($user);
        $locations = Location::where('status', 'on')->get();

            $data['reports_to'] = User::all();
            return view('/content/apps/user/create-edit', compact('page_data', 'user', 'data', 'roles', 'userslist', 'Subdepartments', 'departments', 'associatedSubDepartmentId','locations'));
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param $encrypted_id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updateProfile(UpdateUserProfileRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $selectedColumns = explode(',', $request->input('selected_columns'));
            // dd($selectedColumns);

            // $userData['username'] = $request->get('username');
            $userData['selected_fields'] = json_encode($selectedColumns);
            $userData['first_name'] = $request->get('first_name');
            $userData['last_name'] = $request->get('last_name');
            $userData['location_id'] = $request->get('location_id');
            $userData['email'] = $request->get('email');
            $userData['phone_no'] = $request->get('phone_no');
            if ($request->hasFile('profile_img')) {
                $imagePath = $request->file('profile_img')->store('profile_img', 'public');
                $userData['profile_img'] = $imagePath;
            }

            if ($request->get('password') != null && $request->get('password') != '') {
                $userData['password'] = Hash::make($request->get('password'));
            }
            // dd($userData);
            $user = User::where('id', $id)->first();
            $updated = $this->userService->updateUser($id, $userData);
            if (!empty($updated)) {

                return redirect()->back()->with('success', 'Profile updated successfully');
            } else {

                return redirect()->back()->with('error', 'Error while Updating User');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }

    }

    public function update(UpdateUserRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $userData['username'] = $request->get('username');
            $userData['first_name'] = $request->get('first_name');
            $userData['last_name'] = $request->get('last_name');
            $userData['subdepartment'] = $request->get('subdepartment');
            $userData['department_id'] = $request->get('department_id');
            $userData['location_id'] = $request->get('location_id');
            $userData['G7'] = $request->get('G7');
            $userData['email'] = $request->get('email');
            $userData['phone_no'] = $request->get('phone_no');
            if ($request->get('password') != null && $request->get('password') != '') {
                $userData['password'] = Hash::make($request->get('password'));
            }
            $userData['designation'] = $request->get('designation');
            $userData['dob'] = $request->get('dob');
            $userData['address'] = $request->get('address');
            $userData['branch'] = $request->get('branch');
            $userData['form_group'] = $request->get('form_group');
            $userData['report_to'] = $request->get('report_to');
            $userData['authorization'] = $request->get('authorization');
            $userData['can_export_excel'] = $request->get('can_export_excel') == 'on' ? true : false;
            $userData['can_print_reports'] = $request->get('can_print_reports') == 'on' ? true : false;
            $userData['can_remove_tax'] = $request->get('can_remove_tax') == 'on' ? true : false;
            $userData['can_delete_package'] = $request->get('can_delete_package') == 'on' ? true : false;
            $userData['status'] = $request->get('status') == 'on' ? true : false;
            $userData['created_by'] = Auth::id();

            if ($request->hasFile('profile_img')) {
                $imagePath = $request->file('profile_img')->store('profile_img', 'public');
                $userData['profile_img'] = $imagePath;
            }
            $updated = $this->userService->updateUser($id, $userData);
            $user = User::where('id', $id)->first();
            $role = Role::find($request->get('role'));
            $user->syncRoles([]);
            $user->assignRole($role);
            if (!empty($updated)) {
                return redirect()->route("app-users-list")->with('success', 'User Updated Successfully');
            } else {
                return redirect()->back()->with('success', 'User Updated Successfully');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $encrypted_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $deleted = $this->userService->deleteUser($id);
            if (!empty($deleted)) {
                return redirect()->route("app-users-list")->with('success', 'Users Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Users');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-users-list")->with('error', 'Error while editing Users');
        }
    }
    public function getSubDepartmentsName($department_id)
    {
        $subDepartments = SubDepartment::where('department_id', $department_id)->get();

        return response()->json($subDepartments);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Users Imported Successfully!');
    }
    public function login_as(Request $request)
    {
        Auth::loginUsingId($request->id); // Log in user without password
        return redirect('/app/dashboard')->with('success', 'Logged in successfully!');

    }
}
