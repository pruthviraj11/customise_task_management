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

        $userId = auth()->user()->id;
        $usersWithG7 = User::where('G7', 1)->get();
        $user = auth()->user();
        $deleted_task = DB::table('tasks')->whereNotNull('deleted_at')->count();


        $statusinfos = Status::where('status', "on")->orderBy('order_by', 'ASC')->get();



        // dd('heare');
        return view('content.apps.reports.reports_index', compact('usersWithG7'));
    }




    public function getG7Data(Request $request)
    {
        $usersWithG7 = User::where('G7', 1)->get();

        $cdate = date("Y-m-d");

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

        //conceptualization Counts
        foreach ($usersWithG7 as $user) {
            $conceptualizationCounts[$user->id] = TaskAssignee::where('task_status', 1)
                ->where('user_id', $user->id)
                ->count();
        }

        // Scope Define Counts
        foreach ($usersWithG7 as $user) {
            $scopeDefineCounts[$user->id] = TaskAssignee::where('task_status', 3)
                ->where('user_id', $user->id)
                ->count();
        }

        // In Execution Counts
        foreach ($usersWithG7 as $user) {
            $inExecutionCounts[$user->id] = TaskAssignee::where('task_status', 5)
                ->where('user_id', $user->id)
                ->count();
        }

        /// Completed Task Counts
        foreach ($usersWithG7 as $user) {
            $completedCounts[$user->id] = TaskAssignee::whereIn('task_status', [4, 7])
                ->where('user_id', $user->id)
                ->count();

        }
        // Over Due Counts
        foreach ($usersWithG7 as $user) {
            $overdueCounts[$user->id] = TaskAssignee::where('user_id', $user->id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->count();
        }

        ////Total task
        foreach ($usersWithG7 as $user) {
            $totalTaskCounts[$user->id] = TaskAssignee::where('user_id', $user->id)->where('status', 1)
                ->count();
        }

        /// Completed task Percentage
        foreach ($usersWithG7 as $user) {
            $completedPercentage[$user->id] = ($completedCounts[$user->id] / $totalTaskCounts[$user->id]) * 100;
        }

        /// OverDue task Percentage

        foreach ($usersWithG7 as $user) {
            $overDuePercentage[$user->id] = ($overdueCounts[$user->id] / $totalTaskCounts[$user->id]) * 100;
        }

        /// Task Added On Reporting Date
        foreach ($usersWithG7 as $user) {
            $task_added_reporting_date[$user->id] = TaskAssignee::whereDate('created_at', today())
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->count();
        }



        $data = $usersWithG7->map(function ($user) use ($conceptualizationCounts, $scopeDefineCounts, $inExecutionCounts, $overdueCounts, $completedCounts, $totalTaskCounts, $completedPercentage, $overDuePercentage, $task_added_reporting_date) {
            return [
                'name' => $user->first_name . ' ' . $user->last_name,
                'total_task' => $totalTaskCounts[$user->id] ?? 0,
                'total_completed_task' => $completedCounts[$user->id] ?? 0,
                'completion_percent' => $completedPercentage[$user->id] . '%',
                'total_pending_yesterday' => '-',
                'tasks_added_today' => $task_added_reporting_date[$user->id] ?? 0,
                'tasks_completed_today' => '-',
                'total_pending_closing' => '-',
                'overdue_task' => $overdueCounts[$user->id] ?? 0,
                'percent_overdue' => $overDuePercentage[$user->id] . '%',
                'conceptualization' => $conceptualizationCounts[$user->id] ?? 0,
                'scope_defined' => $scopeDefineCounts[$user->id] ?? 0,
                'in_execution' => $inExecutionCounts[$user->id] ?? 0,
            ];
        });

        return DataTables::of($data)->make(true);
    }


}
