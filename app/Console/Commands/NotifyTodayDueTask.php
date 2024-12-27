<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyTodayDueTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:notify-today-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for tasks due today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereNull('deleted_at')->get();
        // $users = User::where('id',31)->get();

        foreach ($users as $user) {
            $taskData = TaskAssignee::select('task_assignees.*', 'tasks.id as id_task')
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->where('task_assignees.user_id', $user->id)->where('task_assignees.due_date', today())->get();




            // Send notification to each user
            foreach ($taskData as $task) {


                $taskViewUrl = route('app-task-view', ['encrypted_id' => encrypt($task->id_task)]); // Assuming a route 'task.view' exists

                // Create the notification message
                $message = 'The Due Date For Task ' . $task->id_task . ' Is Today.<br>
                <a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>';

                // dd($task);
                createNotification(
                    $user->id,
                    $task->id_task,
                    $message,
                    'Created'
                );
            }
        }
        // dd('ll');
        // // Add your task notification logic here
        // app(\App\Http\Controllers\TaskController::class)->notificationForTodayDueTask();
        $this->info('Notifications sent for today\'s due tasks.');
    }
}
