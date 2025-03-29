<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardCUstomizedController;

use App\Http\Controllers\PriorityController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SubDepartmentController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TaskImportController;
use App\Http\Controllers\SubdepartmentImportController;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routesf
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Main Page Route
Route::get('/', function () {
    return redirect('/login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::any('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset Password routes
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');



Route::group(['prefix' => 'auth'], function () {
    Route::get('login-basic', [AuthenticationController::class, 'login_basic'])->name('auth-login-basic');
    Route::get('login-cover', [AuthenticationController::class, 'login_cover'])->name('auth-login-cover');
    Route::get('register-basic', [AuthenticationController::class, 'register_basic'])->name('auth-register-basic');
    Route::get('register-cover', [AuthenticationController::class, 'register_cover'])->name('auth-register-cover');
    Route::get('forgot-password-basic', [AuthenticationController::class, 'forgot_password_basic'])->name('auth-forgot-password-basic');
    Route::get('forgot-password-cover', [AuthenticationController::class, 'forgot_password_cover'])->name('auth-forgot-password-cover');
    Route::get('reset-password-basic', [AuthenticationController::class, 'reset_password_basic'])->name('auth-reset-password-basic');
    Route::get('reset-password-cover', [AuthenticationController::class, 'reset_password_cover'])->name('auth-reset-password-cover');
    Route::get('verify-email-basic', [AuthenticationController::class, 'verify_email_basic'])->name('auth-verify-email-basic');
    Route::get('verify-email-cover', [AuthenticationController::class, 'verify_email_cover'])->name('auth-verify-email-cover');
    Route::get('two-steps-basic', [AuthenticationController::class, 'two_steps_basic'])->name('auth-two-steps-basic');
    Route::get('two-steps-cover', [AuthenticationController::class, 'two_steps_cover'])->name('auth-two-steps-cover');
    Route::get('register-multisteps', [AuthenticationController::class, 'register_multi_steps'])->name('auth-register-multisteps');
    Route::get('lock-screen', [AuthenticationController::class, 'lock_screen'])->name('auth-lock_screen');
});




Route::group(['prefix' => 'app', 'middleware' => 'auth'], function () {
    Route::get('/tasks/data', [DashboardController::class, 'getTaskData'])->name('tasks.data');
    Route::get('users/status-data', [DashboardController::class, 'getUserStatusData'])->name('users.task.status.data');
    Route::get('users/status-hierarchy', [DashboardController::class, 'getUserStatusData_hierarchy'])->name('users.task.status.hierarchy');

    Route::get('users/requested_by_me', [DashboardCUstomizedController::class, 'getRequestedByMeTaskInfo'])->name('users.task.requested_me');
    Route::get('users/requested_to_me', [DashboardCUstomizedController::class, 'getRequestedToMeTaskInfo'])->name('users.task.requested_to_me');

    Route::get('users/total_tasks_details', [DashboardCUstomizedController::class, 'getTotalTaskInfo'])->name('users.total_task_details');


    /// Route For Dashboard data onclick counts Starts
    /////////////////////////   Routes For Requested To Us  Starts      ///////////////////////
    Route::get('/tasks/requested_to_us/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us');
    Route::get('/tasks/requested_to_us/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTasks'])
        ->name('tasks.requested_to_us_list');

    Route::get('/tasks/requested_to_us_status/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_status');
    Route::get('/tasks/requested_to_us_status/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsStatusTasks'])
        ->name('tasks.requested_to_us_status_list');

    Route::get('/tasks/requested_to_us_pending_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_pending_task');
    Route::get('/tasks/requested_to_us_pending_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsPendingTasks'])
        ->name('tasks.requested_to_us_pending_task_list');

    Route::get('/tasks/requested_to_us_over_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_over_due');
    Route::get('/tasks/requested_to_us_over_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsOverDuesTasks'])
        ->name('tasks.requested_to_us_over_due_list');

    Route::get('/tasks/requested_to_us_today_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_today_due');
    Route::get('/tasks/requested_to_us_today_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTodayDuesTasks'])
        ->name('tasks.requested_to_us_today_due_list');

    Route::get('/tasks/requested_to_us_finished_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_finished_task');
    Route::get('/tasks/requested_to_us_finished_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsFinishedTasks'])
        ->name('tasks.requested_to_us_finished_task_list');

    Route::get('/tasks/requested_to_us_total_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_total_task');
    Route::get('/tasks/requested_to_us_total_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTotalTasks'])
        ->name('tasks.requested_to_us_total_task_list');


    Route::get('/tasks/requested_to_us_footer_total/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_footer_total');
    Route::get('/tasks/requested_to_us_footer_total/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsFooterTotalTasks'])
        ->name('tasks.requested_to_us_footer_total_list');


        Route::get('/tasks/requested_to_us_rejected_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_to_us_rejected_task');
    Route::get('/tasks/requested_to_us_rejected_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsRejectedTasks'])
        ->name('tasks.requested_to_us_rejected_task_list');
    /////////////////////////  Routes For Requested To Us  Ends      ///////////////////////


    /////////////////////////  Routes For Requested By Us Starts    ///////////////////////

    Route::get('/tasks/requested_by_us/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us');
    Route::get('/tasks/requested_by_us/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTasks'])
        ->name('tasks.requested_by_us_list');

    Route::get('/tasks/requested_by_us_status/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_status');
    Route::get('/tasks/requested_by_us_status/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsStatusTasks'])
        ->name('tasks.requested_by_us_status_list');

    Route::get('/tasks/requested_by_us_pending_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_pending_task');
    Route::get('/tasks/requested_by_us_pending_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsPendingTasks'])
        ->name('tasks.requested_by_us_pending_task_list');

    Route::get('/tasks/requested_by_us_over_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_over_due');
    Route::get('/tasks/requested_by_us_over_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsOverDuesTasks'])
        ->name('tasks.requested_by_us_over_due_list');

    Route::get('/tasks/requested_by_us_today_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_today_due');
    Route::get('/tasks/requested_by_us_today_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTodayDuesTasks'])
        ->name('tasks.requested_by_us_today_due_list');

    Route::get('/tasks/requested_by_us_finished_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_finished_task');
    Route::get('/tasks/requested_by_us_finished_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsFinishedTasks'])
        ->name('tasks.requested_by_us_finished_task_list');

    Route::get('/tasks/requested_by_us_total_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_total_task');
    Route::get('/tasks/requested_by_us_total_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTotalTasks'])
        ->name('tasks.requested_by_us_total_task_list');

    Route::get('/tasks/requested_by_us_footer_total/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.requested_by_us_footer_total');
    Route::get('/tasks/requested_by_us_footer_total/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedByUsFooterTotalTasks'])
        ->name('tasks.requested_by_us_footer_total_list');

    /////////////////////////  Routes For Requested By Us Ends ///////////////////////

    /////////////////////////  Routes For Total task Starts   ///////////////////////

    Route::get('/tasks/total_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task');
    Route::get('/tasks/total_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTasks'])
        ->name('tasks.total_task_list');

    Route::get('/tasks/total_task_status/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_status');
    Route::get('/tasks/total_task_status/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsStatusTasks'])
        ->name('tasks.total_task_status_list');

    Route::get('/tasks/total_task_pending_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_pending_task');
    Route::get('/tasks/total_task_pending_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsPendingTasks'])
        ->name('tasks.total_task_pending_task_list');

    Route::get('/tasks/total_task_over_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_over_due');
    Route::get('/tasks/total_task_over_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsOverDuesTasks'])
        ->name('tasks.total_task_over_due_list');

    Route::get('/tasks/total_task_today_due/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_today_due');
    Route::get('/tasks/total_task_today_due/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTodayDuesTasks'])
        ->name('tasks.total_task_today_due_list');

    Route::get('/tasks/total_task_finished_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_finished_task');
    Route::get('/tasks/total_task_finished_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsFinishedTasks'])
        ->name('tasks.total_task_finished_task_list');


    Route::get('/tasks/total_task_total_task/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_total_task');
    Route::get('/tasks/total_task_total_task/list/{user_id}/{status_id}/{type}', [TaskController::class, 'requestedToUsTotalTasks'])
        ->name('tasks.total_task_total_task_list');


    Route::get('/tasks/total_task_footer_total/{user_id}/{status_id}/{type}', [TaskController::class, 'index'])
        ->name('tasks.total_task_footer_total');
    Route::get('/tasks/total_task_footer_total/list/{user_id}/{status_id}/{type}', [TaskController::class, 'totalTaskFooterTotalTasks'])
        ->name('tasks.total_task_footer_total_list');

    /////////////////////////  Routes For Total task Ends ///////////////////////
    /// Route For Dashboard data onclick counts Ends


    Route::get('permissions', [RoleController::class, 'permissions_list'])->name('app-permissions-list');
    Route::get('roles/list', [RoleController::class, 'index'])->name('app-roles-list');
    Route::get('send/mail', [MailController::class, 'sendMail'])->name('send-mail');
    // Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard-index');

    Route::get('dashboard_customized', [DashboardCUstomizedController::class, 'index'])->name('dashboard-index');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports-index');
    Route::get('reportsweek', [ReportsController::class, 'reportsweek'])->name('reports-reportsweek');

    Route::get('activity', [DashboardController::class, 'activity'])->name('activity-index');

    Route::get('rejected-tasks', [TaskController::class, 'rejected_task'])->name('rejected-tasks');

    Route::get('/profile/{encrypted_id}', [UsersController::class, 'profile'])->name('profile.show');
    Route::post('/profile/update/{encrypted_id}', [UsersController::class, 'updateProfile'])->name('profile-update');

    // =============================================================================================================================

    //   ROLE AND USER CONTROLLER

    // =============================================================================================================================
    //Notification

    Route::get('notification/reminders', [NotificationController::class, 'getAllRecentNotification'])->name('api-notification-recent');
    Route::get('notification/list', [NotificationController::class, 'index'])->name('app-notifications');
    Route::get('notification/read/{id?}', [NotificationController::class, 'notification_read'])->name('app-notifications-read');
    Route::get('notification/mark/read/{id?}', [NotificationController::class, 'notification_mark_read'])->name('app-notifications-mark-read');
    Route::get('notification/read/all', [NotificationController::class, 'notification_read'])->name('app-notifications-read-all');
    Route::get('notification/getAll', [NotificationController::class, 'getAll'])->name('notifications-get-all');

    //
    // Roles Start
    Route::get('roles/list', [RoleController::class, 'index'])->name('app-roles-list');
    Route::get('roles/getAll', [RoleController::class, 'getAll'])->name('app-roles-get-all');
    Route::post('roles/store', [RoleController::class, 'store'])->name('app-roles-store');
    Route::get('roles/add', [RoleController::class, 'create'])->name('app-roles-add');
    Route::get('roles/edit/{encrypted_id}', [RoleController::class, 'edit'])->name('app-roles-edit');
    Route::put('roles/update/{encrypted_id}', [RoleController::class, 'update'])->name('app-roles-update');
    Route::get('roles/destroy/{encrypted_id}', [RoleController::class, 'destroy'])->name('app-roles-delete');
    /* Roles Routes End */

    //User start
    Route::get('users/list', [UsersController::class, 'index'])->name('app-users-list');
    Route::get('users/add', [UsersController::class, 'create'])->name('app-users-add');
    Route::post('users/store', [UsersController::class, 'store'])->name('app-users-store');
    Route::get('users/edit/{encrypted_id}', [UsersController::class, 'edit'])->name('app-users-edit');
    Route::put('users/update/{encrypted_id}', [UsersController::class, 'update'])->name('app-users-update');
    Route::get('users/destroy/{encrypted_id}', [UsersController::class, 'destroy'])->name('app-users-destroy');
    Route::get('users/getAll', [UsersController::class, 'getAll'])->name('app-users-get-all');
    Route::get('sub-departments/{department_id}', [UsersController::class, 'getSubDepartmentsName'])->name('app-sub-departments');
    //User End

    //Departments start
    Route::get('department/list', [DepartmentController::class, 'index'])->name('app-department-list');
    Route::get('department/add', [DepartmentController::class, 'create'])->name('app-department-add');
    Route::post('department/store', [DepartmentController::class, 'store'])->name('app-department-store');
    Route::get('department/edit/{encrypted_id}', [DepartmentController::class, 'edit'])->name('app-department-edit');
    Route::put('department/update/{encrypted_id}', [DepartmentController::class, 'update'])->name('app-department-update');
    Route::get('department/destroy/{encrypted_id}', [DepartmentController::class, 'destroy'])->name('app-department-destroy');
    Route::get('department/getAll', [DepartmentController::class, 'getAll'])->name('app-department-get-all');
    //Departments End

    //Location Master start
    Route::get('locations/list', [LocationController::class, 'index'])->name('app-locations-list');
    Route::get('locations/add', [LocationController::class, 'create'])->name('app-locations-add');
    Route::post('locations/store', [LocationController::class, 'store'])->name('app-locations-store');
    Route::get('locations/edit/{encrypted_id}', [LocationController::class, 'edit'])->name('app-locations-edit');
    Route::put('locations/update/{encrypted_id}', [LocationController::class, 'update'])->name('app-locations-update');
    Route::get('locations/destroy/{encrypted_id}', [LocationController::class, 'destroy'])->name('app-locations-destroy');
    Route::get('locations/getAll', [LocationController::class, 'getAll'])->name('app-locations-get-all');
    //Location Master End

    //Status start
    Route::get('status/list', [StatusController::class, 'index'])->name('app-status-list');
    Route::get('status/add', [StatusController::class, 'create'])->name('app-status-add');
    Route::post('status/store', [StatusController::class, 'store'])->name('app-status-store');
    Route::get('status/edit/{encrypted_id}', [StatusController::class, 'edit'])->name('app-status-edit');
    Route::put('status/update/{encrypted_id}', [StatusController::class, 'update'])->name('app-status-update');
    Route::get('status/destroy/{encrypted_id}', [StatusController::class, 'destroy'])->name('app-status-destroy');
    Route::get('status/getAll', [StatusController::class, 'getAll'])->name('app-status-get-all');
    //Status End

    //Priority start
    Route::get('priority/list', [PriorityController::class, 'index'])->name('app-priority-list');
    Route::get('priority/add', [PriorityController::class, 'create'])->name('app-priority-add');
    Route::post('priority/store', [PriorityController::class, 'store'])->name('app-priority-store');
    Route::get('priority/edit/{encrypted_id}', [PriorityController::class, 'edit'])->name('app-priority-edit');
    Route::put('priority/update/{encrypted_id}', [PriorityController::class, 'update'])->name('app-priority-update');
    Route::get('priority/destroy/{encrypted_id}', [PriorityController::class, 'destroy'])->name('app-priority-destroy');
    Route::get('priority/getAll', [PriorityController::class, 'getAll'])->name('app-priority-get-all');
    //Priority End

    //SubDepartments start
    Route::get('sub_department/list', [SubDepartmentController::class, 'index'])->name('app-sub_department-list');
    Route::get('sub_department/add', [SubDepartmentController::class, 'create'])->name('app-sub_department-add');
    Route::post('sub_department/store', [SubDepartmentController::class, 'store'])->name('app-sub_department-store');
    Route::get('sub_department/edit/{encrypted_id}', [SubDepartmentController::class, 'edit'])->name('app-sub_department-edit');
    Route::put('sub_department/update/{encrypted_id}', [SubDepartmentController::class, 'update'])->name('app-sub_department-update');
    Route::get('sub_department/destroy/{encrypted_id}', [SubDepartmentController::class, 'destroy'])->name('app-sub_department-destroy');
    Route::get('sub_department/getAll', [SubDepartmentController::class, 'getAll'])->name('app-sub_department-get-all');
    //SubDepartments End

    //Project start
    Route::get('project/list', [ProjectController::class, 'index'])->name('app-project-list');
    Route::get('project/add', [ProjectController::class, 'create'])->name('app-project-add');
    Route::post('project/store', [ProjectController::class, 'store'])->name('app-project-store');
    Route::get('project/edit/{encrypted_id}', [ProjectController::class, 'edit'])->name('app-project-edit');
    Route::put('project/update/{encrypted_id}', [ProjectController::class, 'update'])->name('app-project-update');
    Route::get('project/destroy/{encrypted_id}', [ProjectController::class, 'destroy'])->name('app-project-destroy');
    Route::get('project/getAll', [ProjectController::class, 'getAll'])->name('app-project-get-all');
    //Project End


    //SubDepartments start
    Route::get('project_status/list', [ProjectStatusController::class, 'index'])->name('app-project-status-list');
    Route::get('project_status/add', [ProjectStatusController::class, 'create'])->name('app-project-status-add');
    Route::post('project_status/store', [ProjectStatusController::class, 'store'])->name('app-project-status-store');
    Route::get('project_status/edit/{encrypted_id}', [ProjectStatusController::class, 'edit'])->name('app-project-status-edit');
    Route::put('project_status/update/{encrypted_id}', [ProjectStatusController::class, 'update'])->name('app-project-status-update');
    Route::get('project_status/destroy/{encrypted_id}', [ProjectStatusController::class, 'destroy'])->name('app-project-status-destroy');
    Route::get('project_status/getAll', [ProjectStatusController::class, 'getAll'])->name('app-project-status-get-all');
    //SubDepartments End
    //Email Template start
    Route::get('email-templates/list', [EmailTemplateController::class, 'index'])->name('app-email-templates-list');
    Route::get('email-templates/add', [EmailTemplateController::class, 'create'])->name('app-email-templates-add');
    Route::post('email-templates/store', [EmailTemplateController::class, 'store'])->name('app-email-templates-store');
    Route::get('email-templates/edit/{encrypted_id}', [EmailTemplateController::class, 'edit'])->name('app-email-templates-edit');
    Route::put('email-templates/update/{encrypted_id}', [EmailTemplateController::class, 'update'])->name('app-email-templates-update');
    Route::get('email-templates/destroy/{encrypted_id}', [EmailTemplateController::class, 'destroy'])->name('app-email-templates-destroy');
    Route::get('email-templates/getAll', [EmailTemplateController::class, 'getAll'])->name('app-email-templates-get-all');
    //Email Template End

    // Manual Entry In Database Starts
    Route::get('complete_sub_task_from_task', [TaskController::class, 'complete_sub_task_from_task'])->name('app-complete_sub_task_from_task');
    Route::get('make_closetask_acc', [TaskController::class, 'make_closetask_acc'])->name('app-make_closetask_acc');
    Route::get('add_accepted_date', [TaskController::class, 'add_accepted_date'])->name('app-add_accepted_date');
    Route::get('add_completed_date', [TaskController::class, 'add_completed_date'])->name('app-add_completed_date');
    Route::get('add_close_date', [TaskController::class, 'add_close_date'])->name('app-add_close_date');
    Route::get('add_due_date', [TaskController::class, 'add_due_date'])->name('app-add_due_date');


    // Manual Entry In Database Ends


    //Task start
    Route::get('task/list', [TaskController::class, 'index'])->name('app-task-list');
    Route::get('task/card-view/{type}', [TaskController::class, 'kanban'])->name('app-task-cardView');
    // Route::get('task/card-view/mytask', [TaskController::class, 'kanban'])->name('app-task-kanban-mytask');
    Route::get('task/add', [TaskController::class, 'create'])->name('app-task-add');
    Route::post('task/store', [TaskController::class, 'store'])->name('app-task-store');
    Route::get('task/edit/{encrypted_id}', [TaskController::class, 'edit'])->name('app-task-edit');
    Route::get('task/recurringedit/{encrypted_id}', [TaskController::class, 'recurringedit'])->name('app-task-recurringedit');
    Route::get('task/retrive/{encrypted_id}', [TaskController::class, 'retrive'])->name('app-task-retrive');
    Route::put('task/update/{encrypted_id}', [TaskController::class, 'update'])->name('app-task-update');
    Route::put('task/recurring-update/{encrypted_id}', [TaskController::class, 'recurringUpdate'])->name('app-task-recurring-update');
    Route::get('task/destroy/{encrypted_id}', [TaskController::class, 'destroy'])->name('app-task-destroy');
    Route::get('task/recurring_destroy/{encrypted_id}', [TaskController::class, 'recurringDestroy'])->name('app-task-recurring_destroy');
    Route::get('task/getAll', [TaskController::class, 'getAll'])->name('app-task-get-all');
    // Route::get('task/view/{encrypted_id}', [TaskController::class, 'view'])->name('app-task-view');
    // Route::post('task/getAll/reject/{encrypted_id}', [TaskController::class, 'reject_task'])->name('app-task-reject');
    Route::get('task/getAll/main', [TaskController::class, 'getAll_main'])->name('app-task-get-main');
    Route::get('task/getAll/recurring_main', [TaskController::class, 'getAll_recurring_main'])->name('app-task-get-recurring_main');
    Route::get('task/getAll/due_date_past', [TaskController::class, 'getAll_dueDatePast'])->name('app-task-get-due_date_past');
    Route::get('task/getAll/pending_task', [TaskController::class, 'getAll_pendingTask'])->name('app-task-get-pending_task');
    Route::get('task/getAll/completed_task', [TaskController::class, 'getAll_completedTask'])->name('app-task-get-completed_task');
    Route::get('task/getAll/overall_task', [TaskController::class, 'getAll_overallTask'])->name('app-task-get-overall_task');

    Route::post('task/pin', [TaskController::class, 'pinTask'])->name('app-task-pin');


    Route::get('/export-dashboard_tasks', [DashboardCUstomizedController::class, 'dashboardTaskExport'])->name('export.dashboard_total_tasks');

    Route::get('task/getAll/requested', [TaskController::class, 'getAll_requested'])->name('app-task-get-requested');
    Route::get('task/getAll/accepted', [TaskController::class, 'getAll_accepted'])->name('app-task-get-accepted');
    Route::get('task/getAll/{encrypted_id}', [TaskController::class, 'accept_task'])->name('app-task-accept');
    Route::get('task/view/{encrypted_id}', [TaskController::class, 'view'])->name('app-task-view');
    Route::get('task/recview/{encrypted_id}', [TaskController::class, 'recview'])->name('app-task-recview');
    Route::post('task/getAll/reject/{encrypted_id}', [TaskController::class, 'reject_task'])->name('app-task-reject');
    Route::get('task/getAllForView/{type}', [TaskController::class, 'getAllForView'])->name('app-task-getAllForView-all');
    Route::get('task/updateTaskFromView/{encrypted_id}/{status}', [TaskController::class, 'updateTaskFromView'])->name('app-task-updateTaskFromView-all');
    Route::post('comments', [TaskController::class, 'storeComments'])->name('comments.store');
    Route::get('sub-departments/{department_id}', [TaskController::class, 'getSubDepartments'])->name('app-sub-departments');
    Route::get('users-by-department/{department_id}', [TaskController::class, 'getUsersByDepartment'])->name('app-users-by-department');
    //Task End

    //Reports
    Route::get('/reports/g7-data', [ReportsController::class, 'getG7Data'])->name('reports.g7-data');
    Route::get('/reports/userwise-data', [ReportsController::class, 'userWiseData'])->name('reports.userwise-data');
    Route::get('masters_report/getAll', [ReportsController::class, 'masters_reportgetAll'])->name(name: 'app-masters_report-get-all');
    Route::get('masters_report/list', [ReportsController::class, 'masters_report'])->name('app-masters_report-list');
    Route::post('/get-task-counts', [ReportsController::class, 'getTaskCounts'])->name('get.task.counts');
    //

    // Task List Start
    Route::get('task/accepted', [TaskController::class, 'index'])->name('app-task-accepted');
    Route::get('task/requested', [TaskController::class, 'index'])->name('app-task-requested');
    Route::get('task/main', [TaskController::class, 'index'])->name('app-task-main');
    Route::get('task/recurring_main', [TaskController::class, 'index'])->name('app-task-recurring_main');
    Route::get('task/due_date_past', [TaskController::class, 'index'])->name('app-task-due_date_past');
    Route::get('task/pending_task', [TaskController::class, 'index'])->name('app-task-pending_task');
    Route::get('task/completed_task', [TaskController::class, 'index'])->name('app-task-completed_task');
    Route::get('task/overall_task', [TaskController::class, 'index'])->name('app-task-overall_task');

    Route::get('task/recurring_cancel/{encrypted_id}', [TaskController::class, 'cancel'])->name('app-task-recurring_cancel');




    Route::get('task/accepted/get/all', [TaskController::class, 'getAll'])->name('app-accepted-get-all');
    Route::get('task/requested/get/all', [TaskController::class, 'getAll'])->name('app-requested-get-all');

    Route::get('task/notification_for_today_due_task', [TaskController::class, 'notificationForTodayDueTask'])->name('app-notification-for-today-due-task');

    // Task List End

    Route::get('task/mytask', [TaskController::class, 'index'])->name('app-task-get-mytask');
    Route::get('task/updateTaskNumber', [TaskController::class, 'updateTaskNumber'])->name('app-task-updateTaskNumber');
    Route::get('task/get_task', [TaskController::class, 'getAll_mytask'])->name('app-task-mytask-get');
    Route::get('task/kanban/mytask', [TaskController::class, 'getAll_kanban_mytask'])->name('app-task-kanban-mytask-get');
    Route::get('task/kanban/accepted', [TaskController::class, 'getAll_kanban_accepted'])->name('app-task-kanban-get-accepted');
    Route::get('task/kanban/requested', [TaskController::class, 'getAll_kanban_requested'])->name('app-task-kanban-get-requested');
    Route::get('task/kanban/all', [TaskController::class, 'getAll_kanban_all'])->name('app-task-kanban-get-all');
    Route::get('task/kanban/kanban_total_task', [TaskController::class, 'getAll_kanban_total_task'])->name('app-task-kanban-getAll_total_task-get');
    Route::get('task/kanban/assign_by_me', [TaskController::class, 'getAll_kanban_assign_by_me'])->name('app-task-kanban-assign_by_me');
    Route::get('task/kanban/main', [TaskController::class, 'getAll_kanban_main'])->name('app-task-kanban-main');
    Route::get('task/kanban/due_date_past', [TaskController::class, 'getAll_kanban_dueDatePast'])->name('app-task-kanban-due_date_past');
    Route::get('task/kanban/pending_task', [TaskController::class, 'getAll_kanban_pendingTask'])->name('app-task-kanban-pending_task');
    Route::get('task/kanban/completed_task', [TaskController::class, 'getAll_kanban_completedTask'])->name('app-task-kanban-completed_task');




    // Route::get('task/kanban/kanban_total_task', [TaskController::class, 'getAll_kanban_total_task'])->name('app-task-kanban-getAll_total_task-get');
    Route::post('/subtask/{subtask}', [TaskController::class, 'markAsCompleted'])->name('subtask.complete');
    Route::delete('/subtask/{subtask}', [TaskController::class, 'removeUser'])->name('subtask.removeUser');
    Route::post('/subtask/reopen/{id}', [TaskController::class, 'reopen'])->name('subtask.reopen');

    Route::get('/subtask/{subtask}', [TaskController::class, 'editSubtask'])->name('subtask.edit');

    /////////////////////////////


    // Route to save feedback and rating data
    Route::post('save-feedback', [TaskController::class, 'saveFeedback'])
        ->name('subtask.saveFeedback');
    Route::post('save-reAssignTo', [TaskController::class, 'saveReAssignTo'])
        ->name('subtask.saveReAssignTo');

    ///////////////////////////
    // Update subtask data (for saving the changes)
    Route::post('/subtask/{subtask}', [TaskController::class, 'updateSubtask'])->name('subtask.update');
    Route::get('task/accepted_by_me', [TaskController::class, 'index'])->name('app-task-get-accepted_by_me');
    Route::get('task/getAll_accepted_by_me', [TaskController::class, 'getAll_accepted_by_me'])->name('app-task-getAll_accepted_by_me-get');

    Route::get('task/assign_by_me', [TaskController::class, 'index'])->name('app-task-get-assign_by_me');
    Route::get('task/getAll_assign_by_me', [TaskController::class, 'getAll_assign_by_me'])->name('app-task-getAll_assign_by_me-get');

    Route::get('task/requested_me', [TaskController::class, 'index'])->name('app-task-get-requested_me');
    Route::get('task/getAll_requested_me', [TaskController::class, 'getAll_requested_me'])->name('app-task-getAll_requested_me-get');

    Route::get('task/conceptualization', [TaskController::class, 'index'])->name('app-task-get-conceptualization');
    Route::get('task/getAll_conceptualization', [TaskController::class, 'getAll_conceptualization'])->name('app-task-getAll_conceptualization-get');
    Route::get('/check-tasks', [TaskController::class, 'checkAndCreateTasks'])->name('check-tasks');
    Route::get('task/closed', [TaskController::class, 'index'])->name('app-task-get-close');
    Route::get('task/getAll_close', [TaskController::class, 'getAll_close'])->name('app-task-getAll_close-get');

    // Route::get('task/due_date_past', [TaskController::class, 'index'])->name('app-task-get-due_date_past');
    // Route::get('task/getAll_due_date_past', [TaskController::class, 'getAll_due_date_past'])->name('app-task-getAll_due_date_past-get');

    Route::get('task/deleted', [TaskController::class, 'index'])->name('app-task-get-deleted');
    Route::get('task/getAll_deleted', [TaskController::class, 'getAll_deleted'])->name('app-task-getAll_deleted-get');

    Route::get('task/scope_defined', [TaskController::class, 'index'])->name('app-task-get-scope_defined');
    Route::get('task/getAll_scope_defined', [TaskController::class, 'getAll_scope_defined'])->name('app-task-getAll_scope_defined-get');

    Route::get('task/completed', [TaskController::class, 'index'])->name('app-task-get-completed');
    Route::get('task/getAll_completed', [TaskController::class, 'getAll_completed'])->name('app-task-getAll_completed-get');

    Route::get('task/in_execution', [TaskController::class, 'index'])->name('app-task-get-in_execution');
    Route::get('task/getAll_in_execution', [TaskController::class, 'getAll_in_execution'])->name('app-task-getAll_in_execution-get');

    Route::get('task/hold', [TaskController::class, 'index'])->name('app-task-get-hold');
    Route::get('task/getAll_hold', [TaskController::class, 'getAll_hold'])->name('app-task-getAll_hold-get');

    Route::get('task/admin_acc', [TaskController::class, 'index'])->name('app-task-get-admin_acc');
    Route::get('task/getAll_admin_acc', [TaskController::class, 'getAll_admin_acc'])->name('app-task-getAll_admin_acc-get');
    Route::get('task/admin_req', [TaskController::class, 'index'])->name('app-task-get-admin_req');
    Route::get('task/getAll_admin_req', [TaskController::class, 'getAll_admin_req'])->name('app-task-getAll_admin_req-get');
    Route::get('task/admin_rej', [TaskController::class, 'index'])->name('app-task-get-admin_rej');
    Route::get('task/getAll_admin_rej', [TaskController::class, 'getAll_admin_rej'])->name('app-task-getAll_admin_rej-get');

    Route::get('task/total_deleted', [TaskController::class, 'index'])->name('app-task-get-total_deleted');
    Route::get('task/get_deleted', [TaskController::class, 'getAll_total_deleted'])->name('app-task-total_deleted-get');

    Route::get('task/total_task', [TaskController::class, 'index'])->name('app-task-get-total_task');
    Route::get('task/my_and_team', [TaskController::class, 'index'])->name('app-task-get-my_and_team');

    Route::get('task/getAll_total_task', [TaskController::class, 'getAll_total_task'])->name('app-task-getAll_total_task-get');
    Route::get('task/my_and_team/get', [TaskController::class, 'getAll_team_and_mytask'])->name('app-task-my_and_team-get');

    Route::get('task/team_task', [TaskController::class, 'index'])->name('app-task-get-team_task');
    Route::get('task/team_task/list', [TaskController::class, 'getAll_team_task'])->name('app-task-get-team_task-list');
    Route::get('attachments/{attachmentId}/download', [TaskController::class, 'download'])->name('attachment.download');

    // Route::get('/attachments/{attachmentId}/download', 'AttachmentController@download')->name('attachment.download');
    Route::get('/statuses', [TaskController::class, 'getStatuses'])->name('get-status');
    Route::get('/get-projects', [TaskController::class, 'getProjects'])->name('get-projects');

    Route::get('/created-by-options', [TaskController::class, 'getCreatedByOptions'])->name('get-users');
    Route::get('/department-options', [TaskController::class, 'getDepartmentOptions'])->name('get-departments');

    Route::post('/departments/import', [DepartmentController::class, 'import'])->name('departments.import');

    //export task List
    Route::get('/export-tasks', function () {
        return Excel::download(new TasksExport, 'tasks.xlsx');
    })->name('export-tasks');
    Route::get('/export-total-tasks', [TaskController::class, 'exportTotalTasks'])->name('export-total-tasks');
});
Route::post('users/import', [UsersController::class, 'import'])->name('users.import');
Route::get('login/as/{id}', [UsersController::class, 'login_as'])->name('users.login');

// Route::post()
Route::post('/import-tasks', [TaskImportController::class, 'import'])->name('tasks.import');
Route::post('/update-tasks', [TaskImportController::class, 'import'])->name('tasks.import');
Route::post('/import-subdepartments', [SubdepartmentImportController::class, 'import'])->name('subdepartment.import');

/* Route Apps */
use App\Http\Controllers\ImportController;

Route::post('import', [ImportController::class, 'import'])->name('import');
Route::post('/tasks/import', [TaskController::class, 'importTasks'])->name('tasks.import');


Route::get('/task/get-counts', [DashboardController::class, 'getTaskCounts'])->name('app-task-get-counts');
Route::get('/task-counts', [DashboardController::class, 'getTaskCounts_2'])->name('task.counts');
Route::get('/tasks/total-count', [DashboardController::class, 'getTotalTaskCountAjax'])->name('tasks.totalCount');

Route::any('/upload_task', [DashboardController::class, 'upload_task'])->name('kk');

Route::get('/tasks/team_task', [DashboardController::class, 'team_task'])->name('tasks.team_task');
