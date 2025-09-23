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
use App\Models\Project;
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
            $userStatus = $user->status;
            $statusLabel = $userStatus == 0 ? 'inactive' : ''; // Label only for inactive users
            $statusText = $statusLabel ? ' <span style="color:red; font-weight:bold; font-size:small;">(' . $statusLabel . ')</span>' : '';


            $departmentName = $user->department->department_name ?? 'No Department';

            $totalTasksTillYesterday = TaskAssignee::where('user_id', $user->id)->where('status', 1)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
                ->whereDate('created_at', '<', now()->startOfDay()) // Ensures only tasks created before today
                ->count();

            $totalPendingTasksTillYesterday = TaskAssignee::where('user_id', $user->id)->where('status', 1)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
                // ->whereDate('created_at', '<=', today())
                ->whereDate('created_at', '<', now()->startOfDay())
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();

            $tasksAddedToday = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                ->whereDate('created_at', today())
                ->count();
            // dd($tasksAddedToday);
            $tasksCompletedToday = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                ->whereIn('task_status', [4, 7])
                // ->whereDate('close_date', today())
                ->whereRaw('DATE(completed_date) = ?', [now()->toDateString()])
                ->count();
            $taskReportDate = TaskAssignee::where('user_id', $user->id)
                ->whereIn('task_id', function ($subquery) {
                    $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                })->where('status', 1)
                // ->whereIn('task_status', [4,7])
                ->count();
            // dd($taskReportDate);

            $totalPendingTask = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                ->whereNotIn('task_status', [4, 7, 6])
                // ->whereDate('created_at', today())
                ->count();

            $totalOverdueTasksTillReportDate = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('due_date', '<', now()->subDay())
                ->whereDate('due_date', '<', Carbon::now()->toDateString())
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();
            $totalTasksConceptualization = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 1)
                ->count();

            $totalTasksScopeDefined = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 3)
                ->count();

            $totalTasksInExecution = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('created_at', today())
                ->where('task_status', 5)
                ->whereNotIn('task_status', [4, 7, 6])

                ->count();
            $totalTasksHold = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('created_at', today())
                ->where('task_status', 6)
                ->count();

            $totalStatusCount = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->where('status', 1)
                // ->whereDate('created_at', today())
                ->whereNotIn('task_status', [4, 7, 6])
                ->whereIn('task_status', [1, 3, 5])
                ->count();


            // Add the user data to the table_data array
            $table_data[] = [
                'user_name' => $user->first_name . ' ' . $user->last_name . ' (' . $departmentName . ')' . $statusText,
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
                'totalTasksHold' => $totalTasksHold,
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

        //Data of Particular members starts
        // Active user task counts
        // foreach ($usersWithG7 as $user) {

        //     $users = collect([$user])->merge($this->getAllSubordinates($user));
        //     dd($users);

        //     // Conceptualization Counts
        //     $conceptualizationCounts[$user->id] = TaskAssignee::where('task_status', 1)
        //         ->where('user_id', $user->id)
        //         ->count();
        //     // Scope Define Counts
        //     $scopeDefineCounts[$user->id] = TaskAssignee::where('task_status', 3)
        //         ->where('user_id', $user->id)
        //         ->count();

        //     // In Execution Counts
        //     $inExecutionCounts[$user->id] = TaskAssignee::where('task_status', 5)
        //         ->where('user_id', $user->id)
        //         ->count();

        //     // Completed Task Counts
        //     $completedCounts[$user->id] = TaskAssignee::whereIn('task_status', [4, 7])
        //         ->where('user_id', $user->id)
        //         ->count();



        //     // Overdue Counts
        //     $overdueCounts[$user->id] = TaskAssignee::where('user_id', $user->id)->whereIn('task_id', function ($subquery) {
        //         $subquery->select('id')->from('tasks')->whereNull('deleted_at');
        //     })->where('status', 1)
        //     ->whereDate('due_date', '<', $cdate)
        //     ->whereNotIn('task_status', [4, 7])
        //         ->count();

        //     // Total Task Counts
        //     $totalTaskCounts[$user->id] = TaskAssignee::where('user_id', $user->id)
        //         // ->where('status', 1)
        //         ->count();

        //     // Task Added Today
        //     $task_added_reporting_date[$user->id] = TaskAssignee::where('user_id', $user->id)
        //         ->whereIn('task_id', function ($subquery) {
        //             $subquery->select('id')->from('tasks')->whereNull('deleted_at');
        //         })->where('status', 1)
        //         ->whereDate('created_at', today())
        //         ->count();


        //     // Task Completed Today
        //     $task_completed_reporting_date[$user->id] = TaskAssignee::where('user_id', $user->id)
        //         ->whereIn('task_id', function ($subquery) {
        //             $subquery->select('id')->from('tasks')->whereNull('deleted_at');
        //         })->where('status', 1)
        //         ->whereIn('task_status', [4, 7])
        //         ->whereRaw('DATE(completed_date) = ?', [now()->toDateString()])

        //         ->count();

        //     // dd($task_completed_reporting_date);
        //     // Task pending - Opning and Closing Today

        //     $task_closing_opening_reporting_date[$user->id] = TaskAssignee::whereDate('created_at', '<', now()->startOfDay())
        //         // whereDate('created_at', today())
        //         ->where('user_id', $user->id)
        //         ->where('status', 1)
        //         ->whereIn('task_id', function ($subquery) {
        //             $subquery->select('id')->from('tasks')->whereNull('deleted_at');
        //         })
        //         ->whereNotIn('task_status', [4, 7])
        //         ->count();


        //     // Completed Task Percentage
        //     $completedPercentage[$user->id] = $totalTaskCounts[$user->id] > 0
        //         ? number_format(($completedCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
        //         : 0;

        //     // Overdue Task Percentage
        //     $overDuePercentage[$user->id] = $totalTaskCounts[$user->id] > 0
        //         ? number_format(($overdueCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
        //         : 0;
        // }

        // // Inactive user task counts
        // // foreach ($InactiveusersWithG7 as $user) {
        // //     $inactiveConceptualization += TaskAssignee::where('task_status', 1)
        // //         ->where('user_id', $user->id)
        // //         ->count();
        // //     $inactiveScopeDefine += TaskAssignee::where('task_status', 3)
        // //         ->where('user_id', $user->id)
        // //         ->count();
        // //     $inactiveInExecution += TaskAssignee::where('task_status', 5)
        // //         ->where('user_id', $user->id)
        // //         ->count();
        // //     $inactiveCompleted += TaskAssignee::whereIn('task_status', [4, 7])
        // //         ->where('user_id', $user->id)
        // //         ->count();
        // //     $inactiveCompletedreport += TaskAssignee::whereDate('created_at', today())
        // //         ->where('user_id', $user->id)
        // //         ->where('status', 1)
        // //         ->whereIn('task_status', [4, 7])
        // //         ->count();
        // //     $inactiveOverdue += TaskAssignee::where('user_id', $user->id)
        // //         ->whereNotIn('task_status', [4, 7])
        // //         ->whereDate('due_date', '<', $cdate)
        // //         ->count();
        // //     $inactiveTotalTasks += TaskAssignee::where('user_id', $user->id)
        // //         ->where('status', 1)
        // //         ->count();
        // //     $inactiveTaskAddedToday += TaskAssignee::whereDate('created_at', today())
        // //         ->where('user_id', $user->id)
        // //         ->where('status', 1)
        // //         ->count();
        // // }

        // // Prepare the active user data
        // $data = $usersWithG7->map(function ($user) use ($conceptualizationCounts, $scopeDefineCounts, $inExecutionCounts, $overdueCounts, $completedCounts, $totalTaskCounts, $completedPercentage, $overDuePercentage, $task_added_reporting_date, $task_completed_reporting_date, $task_closing_opening_reporting_date) {

        //     return [

        //         'name' => $user->first_name . ' ' . $user->last_name,
        //         'users_status' => $user->status,
        //         'total_task' => $totalTaskCounts[$user->id] ?? 0,
        //         'total_completed_task' => $completedCounts[$user->id] ?? 0,
        //         'completion_percent' => $completedPercentage[$user->id] . '%',
        //         'total_pending_yesterday' => $task_closing_opening_reporting_date[$user->id] ?? 0,
        //         'tasks_added_today' => $task_added_reporting_date[$user->id] ?? 0,
        //         'tasks_completed_today' => $task_completed_reporting_date[$user->id] ?? 0,
        //         'total_pending_closing' => $task_closing_opening_reporting_date[$user->id] ?? 0,
        //         'overdue_task' => $overdueCounts[$user->id] ?? 0,
        //         'percent_overdue' => $overDuePercentage[$user->id] . '%',
        //         'conceptualization' => $conceptualizationCounts[$user->id] ?? 0,
        //         'scope_defined' => $scopeDefineCounts[$user->id] ?? 0,
        //         'in_execution' => $inExecutionCounts[$user->id] ?? 0,
        //     ];
        // });

        //Data of Particular member ends


        //Data of Particular member and their team starts

        foreach ($usersWithG7 as $user) {
            $allUsers = collect([$user])->merge($this->getAllSubordinates($user)); // user + team members

            // Initialize counts
            $conceptualizationCounts[$user->id] = 0;
            $scopeDefineCounts[$user->id] = 0;
            $inExecutionCounts[$user->id] = 0;
            $completedCounts[$user->id] = 0;
            $overdueCounts[$user->id] = 0;
            $totalTaskCounts[$user->id] = 0;
            $task_added_reporting_date[$user->id] = 0;
            $task_completed_reporting_date[$user->id] = 0;
            $task_closing_opening_reporting_date[$user->id] = 0;

            foreach ($allUsers as $subUser) {

                // Conceptualization Counts
                $conceptualizationCounts[$user->id] += TaskAssignee::where('task_status', 1)
                    ->where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                // Scope Define Counts
                $scopeDefineCounts[$user->id] += TaskAssignee::where('task_status', 3)
                    ->where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                // In Execution Counts
                $inExecutionCounts[$user->id] += TaskAssignee::where('task_status', 5)
                    ->where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                // Completed Task Counts
                $completedCounts[$user->id] += TaskAssignee::whereIn('task_status', [4, 7])
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->where('user_id', $subUser->id)
                    ->count();

                // Overdue Counts
                $overdueCounts[$user->id] += TaskAssignee::where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->where('status', 1)
                    ->whereDate('due_date', '<', now())
                    ->whereNotIn('task_status', [4, 7])
                    ->count();

                // Total Task Counts
                $totalTaskCounts[$user->id] += TaskAssignee::where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();



                // Task Added Today
                $task_added_reporting_date[$user->id] += TaskAssignee::where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->where('status', 1)
                    ->whereDate('created_at', today())
                    ->count();

                // Task Completed Today
                $task_completed_reporting_date[$user->id] += TaskAssignee::where('user_id', $subUser->id)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->where('status', 1)
                    ->whereIn('task_status', [4, 7])
                    ->whereRaw('DATE(completed_date) = ?', [now()->toDateString()])
                    ->count();

                // Task pending - Opening and Closing Today
                $task_closing_opening_reporting_date[$user->id] += TaskAssignee::whereDate('created_at', '<', now()->startOfDay())
                    ->where('user_id', $subUser->id)
                    ->where('status', 1)
                    ->whereNull('task_assignees.deleted_at')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->whereNotIn('task_status', [4, 7])
                    ->count();
            }

            // Completed Task Percentage
            $completedPercentage[$user->id] = $totalTaskCounts[$user->id] > 0
                ? number_format(($completedCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
                : 0;

            // Overdue Task Percentage
            $overDuePercentage[$user->id] = $totalTaskCounts[$user->id] > 0
                ? number_format(($overdueCounts[$user->id] / $totalTaskCounts[$user->id]) * 100, 2)
                : 0;
        }

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

        //Data of Particular member and their team ends



        // Prepare totals
        $totals = [
            'name' => 'Total',
            'users_status' => '',
            'total_task' => array_sum($totalTaskCounts),
            'total_completed_task' => array_sum($completedCounts),
            'completion_percent' => number_format((array_sum($completedCounts)) / (array_sum($totalTaskCounts)) * 100, 2) . '%',
            'total_pending_yesterday' => array_sum($task_closing_opening_reporting_date),
            'tasks_added_today' => array_sum($task_added_reporting_date),
            'tasks_completed_today' => array_sum($task_completed_reporting_date),
            'total_pending_closing' => array_sum($task_closing_opening_reporting_date),
            'overdue_task' => array_sum($overdueCounts),
            'percent_overdue' => number_format((array_sum($overdueCounts)) / (array_sum($totalTaskCounts)) * 100, 2) . '%',
            'conceptualization' => array_sum($conceptualizationCounts),
            'scope_defined' => array_sum($scopeDefineCounts),
            'in_execution' => array_sum($inExecutionCounts),
        ];


        // Push total row
        $data->push($totals);


        return DataTables::of($data)->make(true);
    }



    public function reportsweek()
    {
        // Get the previous week's start (Monday) and end (Sunday)
        $startOfLastWeek = now()->startOfWeek()->subWeek(); // Last Monday
        $endOfLastWeek = now()->endOfWeek()->subWeek(); // Last Sunday



        $startOfDuringWeek = now()->startOfWeek(); // Last Monday
        $endOfDuringWeek = now()->endOfWeek(); // Last Sunday
        // dd($startOfDuringWeek,$endOfDuringWeek);

        // $userId = auth()->user()->id;
        $usersWithG7 = User::where('Grad', 'G7')->get();
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
            $userStatus = $user->status;
            $statusLabel = $userStatus == 0 ? 'inactive' : ''; // Label only for inactive users

            // Only append parentheses if the user is inactive
            $statusText = $statusLabel ? ' <span style="color:red; font-weight:bold; font-size:small;">(' . $statusLabel . ')</span>' : '';

            $departmentName = $user->department->department_name ?? 'No Department';

            // Modify queries to get data from last Monday to Sunday (Previous Week)
            $totalTasksLastWeek = TaskAssignee::where('user_id', $user->id) // Tasks created since last Monday
                // ->whereDate('created_at', '<=', $endOfLastWeek) // Tasks created until last Sunday
                ->count();

            $totalPendingTasksLastWeek = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', '<=', $endOfLastWeek)
                ->whereNotIn('task_status', [4, 7, 6]) // Exclude completed, deleted, and canceled
                ->count();

            $tasksAddedLastWeek = TaskAssignee::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $startOfDuringWeek)
                ->whereDate('created_at', '<=', $endOfDuringWeek)
                ->count();

            $tasksCompletedLastWeek = TaskAssignee::where('user_id', $user->id)
                ->where('task_status', 4) // Completed tasks (status 4)
                ->whereDate('created_at', '>=', $startOfDuringWeek)
                ->whereDate('created_at', '<=', $endOfDuringWeek)
                ->count();

            $taskReportDate = TaskAssignee::where('user_id', $user->id)
                // ->whereDate('created_at', today())
                ->count();

            $totalPendingTask = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7])
                ->count();

            $totalOverdueTasksLastWeek = TaskAssignee::where('user_id', $user->id)
                ->whereDate('due_date', '<', now()->subDay())
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();

            $totalTasksConceptualization = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 1) // Conceptualization (status 1)
                ->count();

            $totalTasksScopeDefined = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7, 6])
                ->where('task_status', 3) // Scope Defined (status 3)
                ->count();

            $totalTasksInExecution = TaskAssignee::where('user_id', $user->id)
                ->where('task_status', 5) // Execution tasks (status 5)
                ->whereNotIn('task_status', [4, 7, 6])
                ->count();

            $totalStatusCount = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7, 6])
                ->whereIn('task_status', [1, 3, 5])
                ->count();

            // Add the user data to the table_data array
            $table_data[] = [
                'user_name' => $user->first_name . ' ' . $user->last_name . ' (' . $departmentName . ')' . $statusText,
                'total_tasks_last_week' => $totalTasksLastWeek,
                'total_pending_tasks_last_week' => $totalPendingTasksLastWeek,
                'tasks_added_last_week' => $tasksAddedLastWeek,
                'tasks_completed_last_week' => $tasksCompletedLastWeek,
                'task_report_date' => $taskReportDate,
                'total_pending_task' => $totalPendingTask,
                'total_overdue_tasks_last_week' => $totalOverdueTasksLastWeek,
                'totalTasksConceptualization' => $totalTasksConceptualization,
                'totalTasksScopeDefined' => $totalTasksScopeDefined,
                'totalTasksInExecution' => $totalTasksInExecution,
                'totalStatusCount' => $totalStatusCount
            ];
        }

        return view('content.apps.reports.reports-reportsweek', compact('usersWithG7', 'table_data'));
    }

    public function getAllSubordinates($user)
    {
        $subordinates = $user->subordinates;

        foreach ($subordinates as $subordinate) {
            $subordinates = $subordinates->merge($this->getAllSubordinates($subordinate));
        }

        return $subordinates;
    }


    public function masters_report()
    {
        $userId = Auth()->user()->id;
        $projectOptions = Project::get(); // Fetch all projects
        $statusOptions = Status::where('status', 'on')->where('disabled', 0)->get(); // Fetch all projects
        $loggedInUser = auth()->user();
        if ($userId != 1) {
            $hierarchyUsers = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser)); // Merge logged-in user and their subordinates
            $hierarchyUserIds = $hierarchyUsers->pluck('id')->toArray(); // Get array of all user IDs in hierarchy
        } else {
            $hierarchyUserIds = User::where('status', 1)->pluck('id')->toArray(); // Get array of all user IDs in hierarchy
        }
        // dd($hierarchyUserIds,$userId);
        // Pending tasks count
        $pendingTasksCount = TaskAssignee::whereIn('user_id', $hierarchyUserIds)
            ->whereNotIn('task_status', [4, 7, 6])
            ->count(); // Get the count of pending tasks

        // Overdue tasks count
        $overdueTasksCount = TaskAssignee::whereIn('user_id', $hierarchyUserIds)
            ->whereDate('due_date', '<', now()->subDay()) // Due date is older than yesterday
            ->whereNotIn('task_status', [4, 7, 6])
            ->count(); // Get the count of overdue tasks

        // Calculate pace rate: (pending - overdue) / pending
        $paceRate = 0; // Default to 0 to avoid division by zero
        if ($pendingTasksCount > 0) {
            $paceRate = ($pendingTasksCount - $overdueTasksCount) / $pendingTasksCount;
        }

        // Query for TaskAssignee with user-specific filtering
        $query = TaskAssignee::query();
        $loggedInUser = auth()->user();
        if ($loggedInUser->hasRole('Super Admin')) {
            $query->whereNull('deleted_at');
        } else {
            // User-specific task filters
            $query->whereIn('user_id', $hierarchyUserIds)->whereNull('deleted_at');
        }

        // Return the view with the required variables
        return view('content.apps.reports.master_report_list', compact('projectOptions', 'pendingTasksCount', 'overdueTasksCount', 'paceRate', 'statusOptions'));
    }


    public function masters_reportgetAll(Request $request)
    {
        $userId = Auth()->user()->id;
        $loggedInUser = auth()->user();

        ini_set('memory_limit', '2048M'); // Retain memory limit increase, but we'll use chunking to minimize memory usage

        $hierarchyUsers = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));
        $hierarchyUserIds = $hierarchyUsers->pluck('id')->toArray();

        $query = TaskAssignee::query();
        // Admin fetches tasks by their statuses
        $loggedInUser = auth()->user();
        if ($loggedInUser->hasRole('Super Admin')) {
            $query->whereNull('deleted_at')->orderBy('created_at', 'desc');
        } else {
            // User-specific task filters
            $query->whereIn('user_id', $hierarchyUserIds)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc');
        }


        // Apply project filter if provided
        if ($request->has('project_ids') && !empty($request->project_ids)) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->whereIn('project_id', $request->project_ids); // Use whereIn for multiple project IDs
            });
        }

        // Apply status filter directly on TaskAssignee model if provided
        if ($request->has('status_ids') && !empty($request->status_ids)) {
            $query->whereIn('task_status', $request->status_ids); // Directly filter TaskAssignee by task_status
        }
        // Pending tasks count
        $pendingTasksCount = TaskAssignee::whereIn('user_id', $hierarchyUserIds)
            ->whereNotIn('task_status', [4, 7, 6]) // Exclude completed/archived tasks
            ->when($request->has('project_ids'), function ($q) use ($request) {
                $q->whereHas('task', function ($q) use ($request) {
                    $q->whereIn('project_id', $request->project_ids); // Filter by project IDs
                });
            })
            ->count();

        // Overdue tasks count
        $overdueTasksCount = TaskAssignee::whereIn('user_id', $hierarchyUserIds)
            ->whereDate('due_date', '<', now()->subDay()) // Due date is older than yesterday
            ->whereNotIn('task_status', [4, 7, 6]) // Exclude completed/archived tasks
            ->when($request->has('project_ids'), function ($q) use ($request) {
                $q->whereHas('task', function ($q) use ($request) {
                    $q->whereIn('project_id', $request->project_ids); // Filter by project IDs
                });
            })
            ->count();

        // Calculate pace rate: (pending - overdue) / pending
        $paceRate = 0; // Default to 0 to avoid division by zero
        if ($pendingTasksCount > 0) {
            $paceRate = ($pendingTasksCount - $overdueTasksCount) / $pendingTasksCount;
        }

        $tasks = $query;

        return DataTables::of($tasks)
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('description', function ($row) {
                return ($row->task && $row->task->description) ? $row->task->description : '-';
            })
            ->addColumn('subject', function ($row) {
                return ($row->task && $row->task->subject) ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('created_by_username', function ($row): string {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })
            ->addColumn('status', function ($row) {
                return ($row->task && $row->task->task_status) ? $row->task->taskStatus->status_name : "-";
            })
            ->addColumn('start_date', function ($row) {
                return ($row->task && $row->task->start_date) ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })
            ->addColumn('project', function ($row) {
                return ($row->task && $row->task->project) ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return ($row->department && $row->department_data) ? $row->department_data->department_name : '-';
            })
            ->rawColumns(['title', 'Task_number', 'description', 'subject', 'created_by_username', 'Task_assign_to', 'status', 'start_date', 'due_date', 'project', 'department'])
            ->make(true);
    }
    public function getTaskCounts(Request $request)
    {
        $userId = auth()->id();
        $projectIds = $request->input('project_ids', []);
        $loggedInUser = auth()->user();

        if ($userId != 1) {
            $hierarchyUsers = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));
            $hierarchyUserIds = $hierarchyUsers->pluck('id')->toArray();
        } else {
            // Admin: don't filter by assignee user_id (null means "no filter")
            $hierarchyUserIds = null;
        }

        // Build base query in a closure so we can reuse it safely
        $buildQuery = function () use ($hierarchyUserIds, $projectIds) {
            return TaskAssignee::query()
                ->when($hierarchyUserIds, function ($q) use ($hierarchyUserIds) {
                    $q->whereIn('user_id', $hierarchyUserIds);
                })
                ->whereNotIn('task_status', [4, 7, 6]) // exclude completed/archived
                ->when(!empty($projectIds), function ($q) use ($projectIds) {
                    $q->whereHas('task', function ($q) use ($projectIds) {
                        $q->whereIn('project_id', $projectIds);
                    });
                });
        };

        $pendingTasksCount = $buildQuery()->count();

        $overdueTasksCount = $buildQuery()
            ->whereDate('due_date', '<', now()->subDay())
            ->count();

        $paceRate = $pendingTasksCount > 0 ? ($pendingTasksCount - $overdueTasksCount) / $pendingTasksCount : 0;

        return response()->json([
            'pendingTasksCount' => $pendingTasksCount,
            'overdueTasksCount' => $overdueTasksCount,
            'paceRate' => number_format($paceRate * 100, 2)
        ]);
    }

}
