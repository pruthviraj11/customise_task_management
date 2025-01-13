<?php

namespace App\Http\Controllers;



use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use App\Models\Department;
use App\Models\TaskAssignee;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use Illuminate\Http\Request;

class ReportsController extends Controller
{


    public function index()
    {

        // $userId = auth()->user()->id;
        $usersWithG7 = User::where('Grad', operator: 'G7')->get();
        $user = auth()->user();
        $deleted_task = DB::table('tasks')->whereNotNull('deleted_at')->count();

        $table_data = [];
        $statusinfos = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();

        $loggedInUser = auth()->user();
        $userId = $loggedInUser->id;

        $users = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));

        // Prepare the table data
        $table_data = [];

        foreach ($users as $user) {
            $departmentName = $user->department->department_name ?? 'No Department';

            $totalTasksTillYesterday = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', '<=', now()->subDay())
                ->count();

            $totalPendingTasksTillYesterday = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', '<=', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();

            $tasksAddedToday = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();

            $tasksCompletedToday = TaskAssignee::where('user_id', $user->id)
                ->where('task_status', 4)
                ->whereDate('created_at', today())
                ->count();

            $taskReportDate = TaskAssignee::where('user_id', $user->id)
                // ->whereDate('created_at', today())
                ->count();

            $totalPendingTask = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7, 6])
                ->whereDate('created_at', today())
                ->count();

            $totalOverdueTasksTillReportDate = TaskAssignee::where('user_id', $user->id)
                ->whereDate('due_date', '<', now()->subDay())
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();

            $totalTasksConceptualization = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 1)
                ->count();

            $totalTasksScopeDefined = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 3)
                ->count();

            $totalTasksInExecution = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('task_status', 5)
                ->whereNotIn('task_status', [4, 7, 6])

                ->count();

            $totalStatusCount = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->whereIn('task_status', [1, 3, 5])
                ->count();


            // Add the user data to the table_data array
            $table_data[] = [
                'user_name' => $user->first_name . ' ' . $user->last_name . ' (' . $departmentName . ')',
                'total_tasks_till_yesterday' => $totalTasksTillYesterday,
                'total_pending_tasks_till_yesterday' => $totalPendingTasksTillYesterday,
                'tasks_added_today' => $tasksAddedToday,
                'tasks_completed_today' => $tasksCompletedToday,
                'task_report_date' => $taskReportDate,
                'total_pending_task' => $totalPendingTask,
                'total_overdue_tasks_till_report_date' => $totalOverdueTasksTillReportDate,
                'totalTasksConceptualization' => $totalTasksConceptualization,
                'totalTasksScopeDefined' => $totalTasksScopeDefined,
                'totalTasksInExecution' => $totalTasksInExecution,
                'totalStatusCount' => $totalStatusCount
            ];
        }

        // dd('heare');
        return view('content.apps.reports.reports_index', compact('usersWithG7', 'table_data'));
    }




    public function getG7Data(Request $request)
    {
        // Fetch users with G7 flag and active status
        $usersWithG7 = User::where('Grad', operator: 'G7')->get();
        // dd($usersWithG7);
        // Fetch inactive users with G7 flag
        // $InactiveusersWithG7 = User::where('Grad', operator: 'G7')->where('status', 0)->get();

        $cdate = date("Y-m-d");

        // Initialize count arrays
        $conceptualizationCounts = [];
        $scopeDefineCounts = [];
        $inExecutionCounts = [];
        $overdueCounts = [];
        $completedCounts = [];
        $totalTaskCounts = [];
        $completedPercentage = [];
        $overDuePercentage = [];
        $task_added_reporting_date = [];
        $task_completed_reporting_date = [];
        $task_closing_opening_reporting_date = [];

        // Initialize inactive totals
        // $inactiveConceptualization = 0;
        // $inactiveScopeDefine = 0;
        // $inactiveInExecution = 0;
        // $inactiveCompleted = 0;
        // $inactiveCompletedreport = 0;
        // $inactiveOverdue = 0;
        // $inactiveTotalTasks = 0;
        // $inactiveTaskAddedToday = 0;

        // Active user task counts
        foreach ($usersWithG7 as $user) {
            // Conceptualization Counts
            $conceptualizationCounts[$user->id] = TaskAssignee::where('task_status', 1)
               ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            // Scope Define Counts
            $scopeDefineCounts[$user->id] = TaskAssignee::where('task_status', 3)
               ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            // In Execution Counts
            $inExecutionCounts[$user->id] = TaskAssignee::where('task_status', 5)
               ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            // Completed Task Counts
            $completedCounts[$user->id] = TaskAssignee::whereIn('task_status', [4, 7])
               ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            // Overdue Counts
            $overdueCounts[$user->id] = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->count();

            // Total Task Counts
            $totalTaskCounts[$user->id] = TaskAssignee::where('user_id', $user->id)
                ->where('status', 1)
                ->count();

            // Task Added Today
            $task_added_reporting_date[$user->id] = TaskAssignee::whereDate('created_at', today())
               ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('status', 1)
                ->count();

            // Task Completed Today
            $task_completed_reporting_date[$user->id] = TaskAssignee::whereDate('created_at', today())
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('status', 1)
                ->whereIn('task_status', [4, 7])
                ->count();


            // Task pending - Opning and Closing Today

            $task_closing_opening_reporting_date[$user->id] = TaskAssignee::whereDate('created_at', today())
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->where('user_id', $user->id);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('status', 1)
                ->whereNotIn('task_status', [4, 7])
                ->count();


            // Completed Task Percentage
            $completedPercentage[$user->id] = $totalTaskCounts[$user->id] > 0
                ? number_format(($completedCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
                : 0;

            // Overdue Task Percentage
            $overDuePercentage[$user->id] = $totalTaskCounts[$user->id] > 0
                ? number_format(($overdueCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
                : 0;
        }

        // Inactive user task counts
        // foreach ($InactiveusersWithG7 as $user) {
        //     $inactiveConceptualization += TaskAssignee::where('task_status', 1)
        //         ->where('user_id', $user->id)
        //         ->count();
        //     $inactiveScopeDefine += TaskAssignee::where('task_status', 3)
        //         ->where('user_id', $user->id)
        //         ->count();
        //     $inactiveInExecution += TaskAssignee::where('task_status', 5)
        //         ->where('user_id', $user->id)
        //         ->count();
        //     $inactiveCompleted += TaskAssignee::whereIn('task_status', [4, 7])
        //         ->where('user_id', $user->id)
        //         ->count();
        //     $inactiveCompletedreport += TaskAssignee::whereDate('created_at', today())
        //         ->where('user_id', $user->id)
        //         ->where('status', 1)
        //         ->whereIn('task_status', [4, 7])
        //         ->count();
        //     $inactiveOverdue += TaskAssignee::where('user_id', $user->id)
        //         ->whereNotIn('task_status', [4, 7])
        //         ->whereDate('due_date', '<', $cdate)
        //         ->count();
        //     $inactiveTotalTasks += TaskAssignee::where('user_id', $user->id)
        //         ->where('status', 1)
        //         ->count();
        //     $inactiveTaskAddedToday += TaskAssignee::whereDate('created_at', today())
        //         ->where('user_id', $user->id)
        //         ->where('status', 1)
        //         ->count();
        // }

        // Prepare the active user data
        $data = $usersWithG7->map(function ($user) use ($conceptualizationCounts, $scopeDefineCounts, $inExecutionCounts, $overdueCounts, $completedCounts, $totalTaskCounts, $completedPercentage, $overDuePercentage, $task_added_reporting_date, $task_completed_reporting_date, $task_closing_opening_reporting_date) {

            return [
              
                'name' => $user->first_name . ' ' . $user->last_name,
                'users_status' => $user->status,
                'total_task' => $totalTaskCounts[$user->id] ?? 0,
                'total_completed_task' => $completedCounts[$user->id] ?? 0,
                'completion_percent' => $completedPercentage[$user->id] . '%',
                'total_pending_yesterday' => $task_closing_opening_reporting_date[$user->id] ?? 0,
                'tasks_added_today' => $task_added_reporting_date[$user->id] ?? 0,
                'tasks_completed_today' => $task_completed_reporting_date[$user->id] ?? 0,
                'total_pending_closing' => $task_closing_opening_reporting_date[$user->id] ?? 0,
                'overdue_task' => $overdueCounts[$user->id] ?? 0,
                'percent_overdue' => $overDuePercentage[$user->id] . '%',
                'conceptualization' => $conceptualizationCounts[$user->id] ?? 0,
                'scope_defined' => $scopeDefineCounts[$user->id] ?? 0,
                'in_execution' => $inExecutionCounts[$user->id] ?? 0,
            ];
        });

        // Prepare totals
        $totals = [
            'name' => 'Total',
            'users_status' => '',
            'total_task' => array_sum($totalTaskCounts),
            'total_completed_task' => array_sum($completedCounts), // Added missing comma here
            'completion_percent' => number_format((array_sum($completedCounts)) / (array_sum($totalTaskCounts)) * 100, 2) . '%',
            'total_pending_yesterday' =>array_sum($task_closing_opening_reporting_date),
            'tasks_added_today' => array_sum($task_added_reporting_date),
            'tasks_completed_today' => array_sum($task_completed_reporting_date),
            'total_pending_closing' =>array_sum($task_closing_opening_reporting_date),
            'overdue_task' => array_sum($overdueCounts),
            'percent_overdue' => number_format((array_sum($overdueCounts)) / (array_sum($totalTaskCounts)) * 100, 2) . '%',
            'conceptualization' => array_sum($conceptualizationCounts),
            'scope_defined' => array_sum($scopeDefineCounts),
            'in_execution' => array_sum($inExecutionCounts),
        ];


        // Push total row
        $data->push($totals);

        // Prepare inactive row
        // $inactiveRow = [
        //     'name' => 'Inactive',
        //     'total_task' => $inactiveTotalTasks,
        //     'total_completed_task' => $inactiveCompleted,
        //     'completion_percent' => number_format(($inactiveCompleted / $inactiveTotalTasks) * 100, 2) . '%',
        //     'total_pending_yesterday' => '-',
        //     'tasks_added_today' => $inactiveTaskAddedToday,
        //     'tasks_completed_today' => $inactiveCompletedreport,
        //     'total_pending_closing' => '-',
        //     'overdue_task' => $inactiveOverdue,
        //     'percent_overdue' => number_format(($inactiveOverdue / $inactiveTotalTasks) * 100, 2) . '%',
        //     'conceptualization' => $inactiveConceptualization,
        //     'scope_defined' => $inactiveScopeDefine,
        //     'in_execution' => $inactiveInExecution,
        // ];

        // Add inactive row
        // $data->push($inactiveRow);

        // Return data as JSON for DataTables
        return DataTables::of($data)->make(true);
    }



    public function getAllSubordinates($user)
    {
        $subordinates = $user->subordinates;

        foreach ($subordinates as $subordinate) {
            $subordinates = $subordinates->merge($this->getAllSubordinates($subordinate));
        }

        return $subordinates;
    }

}
