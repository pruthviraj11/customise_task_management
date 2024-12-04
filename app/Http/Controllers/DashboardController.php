<?php

namespace App\Http\Controllers;



use App\Services\RoleService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Task;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;

        $my_task = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where(function ($query) use ($userId) {
                $query->where('tasks.created_by', $userId)
                    ->Where('task_assignees.user_id', $userId);
            })
            ->where('task_assignees.status', 1)
            ->count();
        // $my_task = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('tasks.created_by', $userId)
        //     ->where('task_assignees.user_id', $userId)->count();
        $taccepted_by_me = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.status', 1)
            ->where('tasks.created_by', '!=', $userId)
            ->count();
        $mytotal_task = $my_task + $taccepted_by_me;
        $assign_by_me = DB::table('tasks')
            ->where('created_by', '=', $userId)
            ->whereNotExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('task_assignees')
                    ->whereRaw('tasks.id = task_assignees.task_id')
                    ->where('user_id', '=', $userId);
            })
            ->count();


        $requested_me = DB::table('tasks')
            ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', '=', $userId)
            ->where('task_assignees.status', '=', 0)
            ->where('tasks.created_by', '!=', $userId)
            ->count();

        $task_count['conceptualization'] = Task::where('task_status', 1)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->count();
        // $task_count['due_date_past'] = Task::where('task_status', 2)
        //     ->whereHas('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId)
        //             ->where('status', 1);
        //     })
        //     ->count();
        $today = now()->toDateString();
        // dd($today);
        // $task_count['due_date_past'] = Task::where('due_date', '<', $today) // Compare due_date with today's date
        //     ->whereHas('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId)
        //             ->where('status', 1);
        //     })
        //     ->count();
        // dd($task_count);

        $task_count['due_date_past'] = Task::where('due_date', '<', now()) // Select tasks with due date in the past
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter tasks assigned to authenticated user
            })
            ->with([
                'assignees' => function ($query) use ($userId) {
                    $query->where('user_id', $userId); // Load only assignees for authenticated user
                }
            ])
            ->count();

        $task_count['scope_defined'] = Task::where('task_status', 3)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->count();

        $task_count['completed'] = Task::where('task_status', 4)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->count();
        $task_count['in_execution'] = Task::where('task_status', 5)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->count();
        $task_count['hold'] = Task::where('task_status', 6)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->count();
        $total['req_task'] = '';
        $total['acc_task'] = '';
        $total['rej_task'] = '';
        if (auth()->user()->id == 1) {
            $total['req_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 0);
            })->count();

            $total['acc_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 1);
            })->count();

            $total['rej_task'] = Task::whereHas('assignees', function ($query) {
                $query->where('status', 2);
            })->count();

            $task_count['conceptualization'] = Task::where('task_status', '1')->count();

            // $task_count['due_date_past'] = Task::where('task_status', '2')->count();
            // $task_count['due_date_past'] = Task::where('task_status', '2')->count();
            $task_count['scope_defined'] = Task::where('task_status', '3')->count();
            $task_count['completed'] = Task::where('task_status', '4')->count();
            $task_count['in_execution'] = Task::where('task_status', '5')->count();
            $task_count['hold'] = Task::where('task_status', '6')->count();
            $total['total_task'] = $task_count['conceptualization'] + $task_count['scope_defined'] + $task_count['completed'] + $task_count['in_execution'] + $task_count['hold'];
            // dd($task_count, $total);

        }


        return view('content.apps.dashboard.index', compact('my_task', 'total', 'taccepted_by_me', 'task_count', 'assign_by_me', 'requested_me'));
    }

    public function my_task()
    {

        return view('content.apps.dashboard.index');
    }
}
