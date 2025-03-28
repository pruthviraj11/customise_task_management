<?php

namespace App\Http\Controllers;



use App\Exports\DashboardTaskExport;
use App\Models\TaskAssignee;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use App\Models\Department;
use Maatwebsite\Excel\Facades\Excel;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use Illuminate\Http\Request;

class DashboardCUstomizedController extends Controller
{
    public function index()
    {

        $userId = auth()->user()->id;
        $usersWithG7 = User::where('Grad', operator: 'G7')->get();
        $user = auth()->user();
        $deleted_task = DB::table('tasks')->whereNotNull('deleted_at')->count();
        $task_count['conceptualization'] = Task::where('task_status', 1)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
        $today = now()->toDateString();
        $task_count['due_date_past'] = Task::where('task_status', '!=', '7')
            ->where('due_date', '<', today()) // Consider due date passed as of today
            ->where('completed_date', '>', 'due_date') // Ensure completed date is after due date
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter tasks assigned to authenticated user
            })
            ->with([
                'assignees' => function ($query) use ($userId) {
                    $query->where('user_id', $userId); // Load only assignees for authenticated user
                }
            ])
            ->count();

        // parth changes as per requrment

        $task_count['scope_defined'] = Task::where('task_status', 3)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            })
            ->count();

        $task_count['completed'] = Task::where('task_status', 4)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            })
            ->count();
        $task_count['close'] = Task::where('task_status', 7)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            })
            ->count();
        // dd($task_count, $userId);
        $task_count['in_execution'] = Task::where('task_status', 5)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);

            })
            ->count();
        $task_count['hold'] = Task::where('task_status', 6)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            })
            ->count();
        // dd($task_count);
        $total['req_task'] = '';
        $total['acc_task'] = '';
        $total['rej_task'] = '';

        $tasks = [];
        $data = [];
        $statuses = [];
        $statuses = Status::where('status', "on")->get();

        $departments = [];
        $taskCountMatrix = [];
        $total['deleted'] = Task::onlyTrashed()->where('deleted_by', $userId)->count();
        if (auth()->user()->id == 1 || $user->hasrole('Super Admin')) {
            $total['deleted'] = Task::onlyTrashed()->count();

            // $onlySoftDeleted = Model::onlyTrashed()->get();
            $total['req_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 0);
            })->whereNot('task_status', 7)->count();

            $total['acc_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 1);
            })->count();

            $total['rej_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 2);
            })->count();

            $task_count['conceptualization'] = Task::where('task_status', '1')->count();


            $task_count['due_date_past'] = Task::where('task_status', '!=', '7')
                ->where('due_date', '<', Carbon::today())
                ->where(function ($query) {
                    $query->whereNull('completed_date') // Consider tasks not completed yet
                        ->orWhere('completed_date', '>', DB::raw('due_date')); // Or completed date is greater than due date
                })
                ->count();
            // parth changes as per requrment

            $task_count['scope_defined'] = Task::where('task_status', '3')->count();
            $task_count['completed'] = Task::where('task_status', '4')->count();
            $task_count['in_execution'] = Task::where('task_status', '5')->count();
            $task_count['hold'] = Task::where('task_status', '6')->count();
            $task_count['close'] = Task::where('task_status', '7')->count();
            $total['total_task'] = $task_count['conceptualization'] + $task_count['close'] + $task_count['scope_defined'] + $task_count['completed'] + $task_count['in_execution'] + $task_count['hold'];
            // dd($task_count, $total);
            $statuses = Status::where('status', "on")->get();
            $departments = Department::where('status', 'on')->get();

            // Fetch the task counts grouped by status and department
            $taskCounts = DB::table('tasks')
                ->select('task_status', 'department_id', DB::raw('count(*) as total'))
                ->whereNull('deleted_at')
                ->groupBy('task_status', 'department_id')
                ->get();

            // Restructure data for easier access in the view
            $taskCountMatrix = [];
            foreach ($taskCounts as $count) {
                $taskCountMatrix[$count->task_status][$count->department_id] = $count->total;
            }


        }

        // function getHierarchy($userId, &$allUsers, &$addedUserIds)
        // {
        //     $reportingUsers = User::where('report_to', $userId)->get();
        //     foreach ($reportingUsers as $user) {
        //         if (!in_array($user->id, $addedUserIds)) {
        //             $allUsers[$user->id] = $user;
        //             $addedUserIds[] = $user->id;
        //             getHierarchy($user->id, $allUsers, $addedUserIds);
        //         }
        //     }
        // }

        // $allUsers = [];
        // $addedUserIds = [$userId];
        // getHierarchy($userId, $allUsers, $addedUserIds);

        // $query = Task::query();
        // $query->where(function ($query) use ($addedUserIds, $userId) {
        //     $query->whereIn('created_by', $addedUserIds)
        //         ->where('created_by', '!=', $userId)
        //         ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
        //             $q->whereIn('user_id', $addedUserIds)
        //                 ->where('user_id', '!=', $userId);
        //         });
        // });

        // $teamTasks = $query->count();


        // $allActivityLogs = collect();




        //  $MeAndTeam = $this->getTotalTaskCount();
        $MeAndTeam = 00;
        $teamTasks = 00;

        $statusinfos = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();


        $total_task_count = TaskAssignee::leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
            ->whereNull('task_assignees.deleted_at')
            // ->where('task_assignees.status',1)
            ->count();

        // dd('heare');
        return view('content.apps.dashboard.customized_index', compact('MeAndTeam', 'teamTasks', 'usersWithG7', 'data', 'total', 'statuses', 'departments', 'taskCountMatrix', 'deleted_task', 'task_count', 'statusinfos', 'total_task_count'));
    }

    public function dashboardTaskExport()
    {
        return Excel::download(new DashboardTaskExport, 'tasks.xlsx');
    }
    public function activity()
    {
        $userId = auth()->user()->id;

        function getHierarchy($userId, &$allUsers, &$addedUserIds)
        {
            $reportingUsers = User::where('report_to', $userId)->get();
            foreach ($reportingUsers as $user) {
                if (!in_array($user->id, $addedUserIds)) {
                    $allUsers[$user->id] = $user;
                    $addedUserIds[] = $user->id;
                    getHierarchy($user->id, $allUsers, $addedUserIds);
                }
            }
        }

        $allUsers = [];
        $addedUserIds = [$userId];
        getHierarchy($userId, $allUsers, $addedUserIds);

        // Extracting all user IDs from the $allUsers array
        $userIds = array_keys($allUsers);

        // Adding the root user ID to the list of user IDs
        $userIds[] = $userId;



        $allActivityLogs = collect();

        foreach ($userIds as $userId) {
            $activityLog = Activity::orderBy('created_at', 'desc')
                ->where('causer_id', $userId)
                ->limit(15)
                ->get();

            $allActivityLogs = $allActivityLogs->merge($activityLog); // Merge each user's activity logs into the collection
        }
        $allActivityLogs = $allActivityLogs->sortByDesc('created_at');
        if ($allActivityLogs) {
            return view('content.apps.dashboard.activity', ['activityLogs' => $allActivityLogs]);
        } else {
            abort(404, 'Activity log entry not found.');
        }
    }
    public function my_task()
    {

        return view('content.apps.dashboard.index');
    }
    public function getTaskData()
    {
        $statuses = Status::where('status', "on")->get();
        $departments = Department::where('status', "on")->get();

        // Fetch the task counts grouped by department and status
        $taskCounts = DB::table('tasks')
            ->select('department_id', 'task_status', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('department_id', 'task_status')
            ->get();

        // Restructure data for easier access in the view
        $taskCountMatrix = [];
        foreach ($taskCounts as $count) {
            $taskCountMatrix[$count->department_id][$count->task_status] = $count->total;
        }

        $data = [];
        foreach ($departments as $department) {
            $row = [
                'department_name' => $department->department_name,
                'hod' => ($department->user != null) ? ($department->user->first_name ?? '-') . ' ' . ($department->user->last_name ?? '-') : '-',
            ];
            $total = 0;
            foreach ($statuses as $status) {
                $count = $taskCountMatrix[$department->id][$status->id] ?? 0;
                $row['status_' . $status->id] = $count;
                $total += $count;
            }
            $row['total'] = $total;
            $data[] = $row;
        }
        return response()->json(['data' => $data]);
    }


    public function getUserStatusData()
    {
        // Get all users with email
        $users = User::whereNotNull('email')->get();

        // Get distinct task statuses
        $status = Status::where('status', 'on')->get();

        $table_data = [];

        // Get the count of tasks assigned to users grouped by user and task status
        $taskCounts = DB::table('task_assignees')
            ->join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
            ->select('task_assignees.user_id', 'tasks.task_status', DB::raw('count(*) as total'))
            ->whereNull('task_assignees.deleted_at')
            ->where('task_assignees.status', 1)
            ->groupBy('task_assignees.user_id', 'tasks.task_status')
            ->get();

        // Restructure data for easier access in the view
        // dd($taskCounts);

        foreach ($users as $user) {
            $array = [
                'user_name' => $user->first_name . ' ' . $user->last_name,
                // Check if department and hod_data are not null before accessing attributes
                'hod' => ($user->department && $user->department->hod_data)
                    ? ($user->department->hod_data->first_name ?? '-') . ' ' . ($user->department->hod_data->last_name ?? '-')
                    : '-',
                'department' => ($user->department && $user->department->department_name)
                    ? ($user->department->department_name ?? '-')
                    : '-',
                'report_to' => ($user->reportsTo && $user->reportsTo->first_name)
                    ? ($user->reportsTo->first_name . ' ' . $user->reportsTo->last_name ?? '-')
                    : '-',
            ];
            // $user->subdepartment = $user->department_id;
            // $user->save();
            $total = 0;
            foreach ($status as $s) {

                $array[\Str::slug($s->status_name, '_')] = Task::where('task_status', $s->id)
                    ->whereHas('assignees', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->count();
                $total += $array[\Str::slug($s->status_name, '_')];
            }
            $array['total'] = $total;
            array_push($table_data, $array);
        }


        return response()->json(['data' => $table_data]);
    }


    public function getUserStatusData_hierarchy()
    {
        $loggedInUser = auth()->user();

        // Get all users under the logged-in user (recursively)
        $users = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));

        // Get distinct task statuses
        $statuses = Status::where('status', 'on')->get();

        $table_data = [];

        foreach ($users as $user) {
            $array = [
                'user_name' => $user->first_name . ' ' . $user->last_name,
                // 'hod' => ($user->department && $user->department->hod_data)
                //     ? ($user->department->hod_data->first_name ?? '-') . ' ' . ($user->department->hod_data->last_name ?? '-')
                //     : '-',
                // 'department' => ($user->department && $user->department->department_name)
                //     ? ($user->department->department_name ?? '-')
                //     : '-',
            ];

            $total = 0;
            foreach ($statuses as $status) {
                $array[\Str::slug($status->status_name, '_')] = Task::where('task_status', $status->id)
                    ->whereHas('assignees', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->count();
                $total += $array[\Str::slug($status->status_name, '_')];
            }
            $array['total'] = $total;
            array_push($table_data, $array);
        }

        return response()->json(['data' => $table_data]);
    }
    // Function to get the total task count


    function getHierarchyUsers($userId)
    {
        return $userId;
    }


    // function requestedBymeTasks($userId)
    // {
    //     $users = DB::table('task_assignees')
    //         ->join('users', 'task_assignees.user_id', '=', 'users.id')
    //         ->select('task_assignees.*', 'users.first_name as first_name', 'users.last_name as last_name')
    //         ->where('task_assignees.user_id', '=', $userId)
    //         ->get();

    //     return $users;
    // }



    public function getAllSubordinates($user)
    {
        $subordinates = $user->subordinates;

        foreach ($subordinates as $subordinate) {
            // Recursively get the subordinates of each subordinate
            $subordinates = $subordinates->merge($this->getAllSubordinates($subordinate));
        }

        return $subordinates;
    }




    /*----------  Requested to me flow ----------*/
    public function getRequestedToMeTaskInfo()
    {
        $loggedInUser = auth()->user();
        $userId = $loggedInUser->id;

        $users = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));


        //$status = Status::where('status', 'on')->get();
        $status = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();

        $table_data = [];

        foreach ($users as $user) {
            $userStatus = $user->status;
            $statusLabel = $userStatus == 0 ? 'inactive' : ''; // Label only for inactive users

            // Only append parentheses if the user is inactive
            $statusText = $statusLabel ? ' <span style="color:red; font-weight:bold; font-size:small;">(' . $statusLabel . ')</span>' : '';

            $array = [
                'user_id' => ($user->id),
                'user_name' => $user->first_name . ' ' . $user->last_name . $statusText, // Append the styled status label
            ];



            $totalAssign = TaskAssignee::where('user_id', $user->id)->where('status', '0')
            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->count();

            $array['requested_to_us'] = $totalAssign; // Correctly add the key-value pair to the existing array

            $total = 0;
            $pending_total = 0;
            $finish_total = 0;
            $matchIds = [1, 3, 5, 6];
            $complete_close = ['4', '7'];

            $cdate = date("Y-m-d");


            foreach ($status as $i => $s) {


                $CountTaskStatus = TaskAssignee::where('user_id', $user->id)
                    ->where('task_status', $s->id)
                    ->where('status','1')
                    // ->where('created_by', $user->id)
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                $array[\Str::slug($s->status_name, '_')] = $CountTaskStatus;
                $array['status_id'] = $s->id;

                /*------  Total PendingTask Detais -----*/

                if (in_array($s->id, $matchIds)) {
                    $CountPendingTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                    ->where('status','1')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                        // ->where('created_by', $user->id)
                        ->count();
                    $pending_total += $CountPendingTask;
                }

                /*---------------  Total Dues Tasks ------*/

                $due_tasks = TaskAssignee::where('user_id', $user->id)
                    // ->where('created_by', $user->id)
                    ->whereNotIn('task_status', [4, 7])
                    ->where('status','1')

                    ->whereDate('due_date', '<', $cdate)
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();


                // $due_tasks = TaskAssignee::where('user_id', $userId)
                //     ->where('created_by', $user->id)
                //     ->whereNotIn('task_status', [4, 7])
                //     ->get();


                // $totalDues = 0;
                // foreach ($due_tasks as $due_task) {
                //     $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->count();
                //     $totalDues += $countTotalTask;
                // }

                $array['over_dues'] = $due_tasks;

                /*--------------- Total Today's Due ------*/


                $TodayCountDueTask = TaskAssignee::where('user_id', $user->id)
                    // ->where('created_by', $user->id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->where('status','1')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                // $today_tasks = TaskAssignee::where('user_id', $userId)
                //     ->where('created_by', $user->id)
                //     ->whereNotIn('task_status', [4, 7])
                //     ->get();

                // $totalTodayDues = 0;
                // foreach ($today_tasks as $today_task) {
                //     $countTotalTask = Task::where('id', $today_task->task_id)->where('due_date', '=', $cdate)->count();
                //     $totalTodayDues += $countTotalTask;
                // }

                $array['today_dues'] = $TodayCountDueTask;


                /*--------------  Total Finished Tasks -----*/
                if (in_array($s->id, $complete_close)) {
                    $CountFinishedTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                    ->where('status','1')
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                        // ->where('created_by', $user->id)
                        ->count();
                    $finish_total += $CountFinishedTask;
                }

            }
            $array['pending_tasks'] = $pending_total;
            $array['finish_tasks'] = $finish_total;
            $array['total'] = $pending_total + $finish_total;

            array_push($table_data, $array);
        }
        return response()->json(['data' => $table_data]);

    }




    /*------- Rquested By me Flow--------------*/
    public function getRequestedByMeTaskInfo()
    {
        ini_set('max_execution_time', 500);
        $loggedInUser = auth()->user();
        $userId = $loggedInUser->id;

        $users = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));

        // dd($users);

        //$status = Status::where('status', 'on')->get();
        $status = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();




        $table_data = [];

        foreach ($users as $user) {
            $userStatus = $user->status;
            $statusLabel = $userStatus == 0 ? 'inactive' : ''; // Label only for inactive users

            // Only append parentheses if the user is inactive
            $statusText = $statusLabel ? ' <span style="color:red; font-weight:bold; font-size:small;">(' . $statusLabel . ')</span>' : '';

            $array = [
                'user_id' => ($user->id),
                'user_name' => $user->first_name . ' ' . $user->last_name . $statusText, // Append the styled status label
            ];


            $totalAssign = TaskAssignee::where('user_id', $user->id)->where('status', '0')->where('created_by', $userId)
            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })->count();

            $array['requested_by_us'] = $totalAssign; // Correctly add the key-value pair to the existing array

            $total = 0;
            $pending_total = 0;
            $finish_total = 0;
            $matchIds = [1, 3, 5, 6];
            $complete_close = ['4', '7'];

            $cdate = date("Y-m-d");


            foreach ($status as $i => $s) {


                $CountTaskStatus = TaskAssignee::where('user_id', $user->id)
                    ->where('task_status', $s->id)
                    ->where('created_by', $userId)
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                $array[\Str::slug($s->status_name, '_')] = $CountTaskStatus;
                $array['status_id'] = $s->id;



                /*------  Total PendingTask Detais -----*/

                if (in_array($s->id, $matchIds)) {
                    $CountPendingTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        ->where('created_by', $userId)
                        ->whereIn('task_id', function ($subquery) {
                            $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                        })
                        ->count();
                    $pending_total += $CountPendingTask;
                }

                /*---------------  Total Dues Tasks ------*/

                $CountDueTask = TaskAssignee::where('user_id', $user->id)
                    ->where('created_by', $userId)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();

                // $due_tasks = TaskAssignee::where('user_id', $user->id)
                //     ->where('created_by', $userId)
                //     ->whereNotIn('task_status', [4, 7])
                //     ->get();

                // $totalDues = 0;
                // foreach ($due_tasks as $due_task) {
                //     $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->count();
                //     $totalDues += $countTotalTask;
                // }

                $array['over_dues'] = $CountDueTask;

                /*--------------- Total Today's Due ------*/


                $TodayCountDueTask = TaskAssignee::where('user_id', $user->id)
                    ->where('created_by', $userId)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->whereIn('task_id', function ($subquery) {
                        $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                    })
                    ->count();


                // $today_tasks = TaskAssignee::where('user_id', $user->id)
                //     ->where('created_by', $userId)
                //     ->whereNotIn('task_status', [4, 7])
                //     ->get();

                // $totalTodayDues = 0;
                // foreach ($today_tasks as $today_task) {
                //     $countTotalTask = Task::where('id', $today_task->task_id)->where('due_date', '=', $cdate)->count();
                //     $totalTodayDues += $countTotalTask;
                // }

                $array['today_dues'] = $TodayCountDueTask;


                /*--------------  Total Finished Tasks -----*/
                if (in_array($s->id, $complete_close)) {
                    $CountFinishedTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        ->where('created_by', $userId)
                        ->whereIn('task_id', function ($subquery) {
                            $subquery->select('id')->from('tasks')->whereNull('deleted_at');
                        })
                        ->count();
                    $finish_total += $CountFinishedTask;
                }




            }

            $array['pending_tasks'] = $pending_total;
            $array['finish_tasks'] = $finish_total;
            $array['total'] = $pending_total + $finish_total;
            ;



            array_push($table_data, $array);
        }

        return response()->json(['data' => $table_data]);


    }



    /*----------  Total Task Info -------*/

    public function getTotalTaskInfo()
    {
        $loggedInUser = auth()->user();
        $userId = $loggedInUser->id;

        $users = collect([$loggedInUser])->merge($this->getAllSubordinates($loggedInUser));


        //$status = Status::where('status', 'on')->get();
        $status = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();

        $table_data = [];

        foreach ($users as $user) {
            $userStatus = $user->status;
            $statusLabel = $userStatus == 0 ? 'inactive' : ''; // Label only for inactive users

            // Only append parentheses if the user is inactive
            $statusText = $statusLabel ? ' <span style="color:red; font-weight:bold; font-size:small;">(' . $statusLabel . ')</span>' : '';

            $array = [
                'user_id' => encrypt($user->id),
                'user_name' => $user->first_name . ' ' . $user->last_name . $statusText, // Append the styled status label
            ];


            $RequestToUs = TaskAssignee::where('user_id', $userId)->where('status', '0')->where('created_by', $user->id)->count();

            $RequestByMe = TaskAssignee::where('user_id', $user->id)->where('status', '0')->where('created_by', $userId)->count();

            $array['total_tasks'] = $RequestToUs + $RequestByMe; // Correctly add the key-value pair to the existing array

            $total = 0;
            $pending_total = 0;
            $finish_total = 0;
            $matchIds = [1, 3, 5, 6];
            $complete_close = ['4', '7'];

            $cdate = date("Y-m-d");


            foreach ($status as $i => $s) {


                $CountRequestUs = TaskAssignee::where('user_id', $user->id)
                    ->where('task_status', $s->id)
                    // ->where('created_by', $user->id)
                    ->count();

                $CountRequestTo = TaskAssignee::where('user_id', $user->id)

                    ->where('task_status', $s->id)
                    ->where('created_by', $userId)
                    ->count();

                $array[\Str::slug($s->status_name, '_')] = $CountRequestUs + $CountRequestTo;
                $array['status_id'] = $s->id;



                /*------  Total PendingTask Detais -----*/

                if (in_array($s->id, $matchIds)) {
                    $CountRequestToPendingTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        // ->where('created_by', $user->id)
                        ->count();


                    $CountRequestByMePendingTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        ->where('created_by', $userId)
                        ->count();

                    $pending_total += $CountRequestToPendingTask + $CountRequestByMePendingTask;
                }

                /*---------------  Total Dues Tasks ------*/

                $CountRequestToTask = TaskAssignee::where('user_id', $user->id)
                    // ->where('created_by', $user->id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->count();

                $CountRequestByMeTask = TaskAssignee::where('user_id', $user->id)
                    ->where('created_by', $userId)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->count();




                $array['over_dues'] = $CountRequestToTask + $CountRequestByMeTask;


                /*--------------- Total Today's Due ------*/


                $TodayCountRequestToDueTask = TaskAssignee::where('user_id', $user->id)
                    // ->where('created_by', $user->id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->count();


                $today_requested_me_tasks = TaskAssignee::where('user_id', $user->id)
                    ->where('created_by', $userId)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->count();


                $array['today_dues'] = $TodayCountRequestToDueTask + $today_requested_me_tasks;


                /*--------------  Total Finished Tasks -----*/
                if (in_array($s->id, $complete_close)) {
                    $CountRequestToFinishedTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        // ->where('created_by', $user->id)
                        ->count();

                    $CountRequestByMeFinishedTask = TaskAssignee::where('user_id', $user->id)
                        ->where('task_status', $s->id)
                        ->where('created_by', $userId)
                        ->count();
                    $finish_total += $CountRequestToFinishedTask + $CountRequestByMeFinishedTask;
                }

            }

            $array['pending_tasks'] = $pending_total;
            $array['finish_tasks'] = $finish_total;
            $array['total'] = $pending_total + $finish_total;

            array_push($table_data, $array);
        }

        return response()->json(['data' => $table_data]);

    }





    public function getTotalTaskCount()
    {
        $userId = auth()->user()->id;

        // Query 1: Tasks created by or assigned to the user with task_assignees.status = 1
        $my_task_query = Task::select('tasks.id')
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where(function ($query) use ($userId) {
                $query->where('tasks.created_by', $userId)
                    ->orWhere('task_assignees.user_id', $userId);
            })
            ->where('task_assignees.status', 1);

        // Query 2: Tasks assigned to the user and accepted, but not created by the user
        $taccepted_by_me_query = Task::select('tasks.id')
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->where('tasks.created_by', '!=', $userId);

        // Query 3: Tasks created by the user where the user is not also assigned
        $assign_by_me_query = Task::select('tasks.id')
            ->where('created_by', $userId)
            ->whereDoesntHave('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        // Query 4: Tasks where the user has been assigned but has not accepted yet
        $requested_me_query = Task::select('tasks.id')
            ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $userId)
            ->where('task_assignees.status', 0)
            ->where('tasks.created_by', '!=', $userId)
            ->where('tasks.task_status', '!=', 7);

        // Combine all task queries
        $all_tasks = $my_task_query
            ->union($taccepted_by_me_query)
            ->union($assign_by_me_query)
            ->union($requested_me_query);

        // Hierarchy: Get users who report to the logged-in user
        $allUsers = [];
        $addedUserIds = [$userId];

        // Recursive function to get users in the hierarchy
        $this->getHierarchy($userId, $allUsers, $addedUserIds);

        // Query 5: Tasks created by or assigned to users in the hierarchy, excluding tasks by the logged-in user
        $hierarchical_tasks_query = Task::select('tasks.id')
            ->where(function ($query) use ($addedUserIds, $userId) {
                $query->whereIn('created_by', $addedUserIds)
                    ->where('created_by', '!=', $userId) // Exclude tasks created by the logged-in user
                    ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
                        $q->whereIn('user_id', $addedUserIds)
                            ->where('user_id', '!=', $userId);
                    });
            });

        // Get the count of combined tasks
        $totalTaskCount = $all_tasks
            ->union($hierarchical_tasks_query)
            ->count();

        return $totalTaskCount;
    }

    // Recursive function to get hierarchical users
    // private function getHierarchy($userId, &$allUsers, &$addedUserIds)
    // {
    //     $reportingUsers = User::where('report_to', $userId)->get();
    //     foreach ($reportingUsers as $user) {
    //         if (!in_array($user->id, $addedUserIds)) {
    //             $allUsers[$user->id] = $user;
    //             $addedUserIds[] = $user->id;
    //             $this->getHierarchy($user->id, $allUsers, $addedUserIds);
    //         }
    //     }
    // }

    // Function to get hierarchical users
    public function getHierarchy($userId, &$allUsers, &$addedUserIds)
    {
        $reportingUsers = User::where('report_to', $userId)->get();
        foreach ($reportingUsers as $user) {
            if (!in_array($user->id, $addedUserIds)) {
                $allUsers[$user->id] = $user;
                $addedUserIds[] = $user->id;
                $this->getHierarchy($user->id, $allUsers, $addedUserIds);
            }
        }
    }


    public function getTaskCounts(Request $request)
    {
        $userId = auth()->user()->id;
        $user = auth()->user();
        $my_task_count = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where(function ($query) use ($userId) {
                $query->where('tasks.created_by', $userId)
                    ->Where('task_assignees.user_id', $userId);
            })
            ->where('task_assignees.status', '!=', 2) // Exclude status 2
            ->count();

        // Get tasks accepted by the user but not created by them, with status 1, excluding tasks with a specific condition
        $taccepted_by_me_count = Task::whereHas('assignees', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('status', '1'); // Only count status 1 tasks
        })
            ->where('created_by', '!=', $userId) // Exclude tasks created by the user
            ->count();

        // Get tasks assigned by the user but not assigned to them
        $assign_by_me_count = Task::where('created_by', $userId)
            ->whereDoesntHave('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        // Get tasks requested from the user, excluding tasks with status 7
        $requested_me_count = Task::leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $userId)
            ->where('task_assignees.status', 0) // Only pending tasks
            ->where('tasks.created_by', '!=', $userId)
            ->where('tasks.task_status', '!=', 7) // Exclude tasks with status 7
            ->count();


        $userId = auth()->user()->id;

        // Query 1: Tasks created by or assigned to the user with status 1
        $my_task_query = Task::select('tasks.*') // Select all task fields
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where(function ($query) use ($userId) {
                $query->where('tasks.created_by', $userId)
                    ->orWhere('task_assignees.user_id', $userId);
            })
            ->where('task_assignees.status', 1);

        // Query 2: Tasks assigned to the user and accepted, but not created by the user
        $taccepted_by_me_query = Task::select('tasks.*')
            ->whereHas('assignees', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', '1');
            })
            ->whereNotIn('created_by', [$user->id]);

        // Query 3: Tasks created by the user where the user is not also assigned
        $assign_by_me_query = Task::select('tasks.*')
            ->where('created_by', $userId)
            ->whereDoesntHave('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        // Query 4: Tasks where the user has been assigned but has not accepted yet
        $requested_me_query = Task::select('tasks.*')
            ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $userId)
            ->where('task_assignees.status', 0)
            ->where('tasks.created_by', '!=', $userId)
            ->where('tasks.task_status', '!=', 7);

        // Combine all queries using union
        $all_tasks = $my_task_query
            ->union($taccepted_by_me_query)
            ->union($assign_by_me_query)
            ->union($requested_me_query)
            ->get();

        $total_task_count = $all_tasks->count();



        $my_task = Task::where('created_by', $userId)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', '!=', 2);
            })
            ->withCount('assignees')
            ->having('assignees_count', '=', 1) // Ensure only one assignee
            ->with([
                'attachments',
                'assignees' => function ($query) {
                    $query->select('task_id', 'status', 'remark'); // Customize as needed
                },
                'creator',
                'taskStatus',
                'project',
                'department',
                'sub_department',
                'comments'
            ])->count();


        // $taccepted_by_me = Task::whereHas('assignees', function ($query) use ($userId) {
        //     $query->where('user_id', $userId)->where('status', '1');
        // })
        //     ->whereNotIn('created_by', [$userId])
        //     ->count();

        // $assign_by_me = Task::where('created_by', $userId)
        //     ->whereDoesntHave('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId);
        //     })
        //     ->count();

        // $requested_me = Task::leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('task_assignees.user_id', $userId)
        //     ->where('task_assignees.status', 0)
        //     ->where('tasks.created_by', '!=', $userId)
        //     ->whereNot('tasks.task_status', 7)
        //     ->count();
        // $total_task = $my_task + $taccepted_by_me + $assign_by_me + $requested_me;
        return response()->json([
            'my_task' => $my_task,
            'taccepted_by_me' => $taccepted_by_me_count,
            'assign_by_me' => $assign_by_me_count,
            'requested_me' => $requested_me_count,
            'total_task' => $total_task_count,
            // 'teamTasks'=>$teamTasks,
        ]);
    }

    public function getTaskCounts_2(Request $request)
    {
        $userId = auth()->user()->id;

        // Initialize task counts for the current user
        $task_count = [
            'conceptualization' => Task::where('task_status', 1)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'due_date_past' => Task::where('task_status', '!=', 7)
                ->where('due_date', '<', today())
                ->where(function ($query) {
                    $query->whereNull('completed_date')
                        ->orWhere('completed_date', '>', DB::raw('due_date'));
                })
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'scope_defined' => Task::where('task_status', 3)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'completed' => Task::where('task_status', 4)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'in_execution' => Task::where('task_status', 5)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'hold' => Task::where('task_status', 6)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'close' => Task::where('task_status', 7)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count(),

            'deleted' => Task::onlyTrashed()
                ->where('created_by', $userId)
                ->count(),

            // 'deleted' => DB::table('tasks')->whereNotNull('deleted_at')->where('deleted_by', $userId)->count()

        ];

        // If the user is a Super Admin or has user ID 1, include soft-deleted tasks
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->id == 1) {
            $task_count['deleted'] = Task::onlyTrashed()->count();
            $task_count['conceptualization'] = Task::where('task_status', '1')->count();

            $task_count['due_date_past'] = Task::where('task_status', '!=', '7')
                ->where('due_date', '<', Carbon::today())
                ->where(function ($query) {
                    $query->whereNull('completed_date') // Consider tasks not completed yet
                        ->orWhere('completed_date', '>', DB::raw('due_date')); // Or completed date is greater than due date
                })
                ->count();
            $task_count['scope_defined'] = Task::where('task_status', '3')->count();
            $task_count['completed'] = Task::where('task_status', '4')->count();
            $task_count['in_execution'] = Task::where('task_status', '5')->count();
            $task_count['hold'] = Task::where('task_status', '6')->count();
            $task_count['close'] = Task::where('task_status', '7')->count();
            $total['total_task'] = $task_count['conceptualization'] + $task_count['close'] + $task_count['scope_defined'] + $task_count['completed'] + $task_count['in_execution'] + $task_count['hold'];
            // $task_count['deleted'] = DB::table('tasks')->whereNotNull('deleted_by')->count();
            $task_count['deleted'] = Task::onlyTrashed()->count();


        }

        // Return task counts as a JSON response
        return response()->json(['task_count' => $task_count]);
    }

    public function getTotalTaskCountAjax()
    {
        $totalTaskCount = $this->getTotalTaskCount();

        return response()->json(['total_task_count' => $totalTaskCount]);
    }
    public function upload_task()
    {
        return view('content.apps.dashboard.upload');

    }
    // public function team_task()
    // {
    //     $userId = auth()->user()->id;
    //     function getHierarchy($userId, &$allUsers, &$addedUserIds)
    //     {
    //         $reportingUsers = User::where('report_to', $userId)->get();
    //         foreach ($reportingUsers as $user) {
    //             if (!in_array($user->id, $addedUserIds)) {
    //                 $allUsers[$user->id] = $user;
    //                 $addedUserIds[] = $user->id;
    //                 getHierarchy($user->id, $allUsers, $addedUserIds);
    //             }
    //         }
    //     }


    //     $allUsers = [];

    //     $addedUserIds = [$userId];
    //     getHierarchy($userId, $allUsers, $addedUserIds);

    //     $query = Task::query();
    //     $query->where(function ($query) use ($addedUserIds, $userId) {
    //         $query->whereIn('created_by', $addedUserIds)
    //             ->where('created_by', '!=', $userId)
    //             ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
    //                 $q->whereIn('user_id', $addedUserIds)
    //                     ->where('user_id', '!=', $userId);
    //             });
    //     });

    //     $teamTasks = $query->count();
    //     return response()->json(['teamTasks_count' => $teamTasks]);
    // }
    public function team_task()
    {
        $userId = auth()->user()->id;
        $allUsers = User::where('report_to', $userId)
            ->orWhereIn('id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('users')
                    ->where('report_to', $userId);
            })->pluck('id')->toArray();

        $addedUserIds = array_merge([$userId], $allUsers);

        $teamTasks = Task::where(function ($query) use ($addedUserIds, $userId) {
            $query->whereIn('created_by', $addedUserIds)
                ->where('created_by', '!=', $userId)
                ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
                    $q->whereIn('user_id', $addedUserIds)
                        ->where('user_id', '!=', $userId);
                });
        })->count();

        return response()->json(['teamTasks_count' => $teamTasks]);
    }

}

