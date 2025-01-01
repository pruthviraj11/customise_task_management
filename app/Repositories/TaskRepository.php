<?php
namespace App\Repositories;

use App\Models\RecurringTask;
use App\Models\Task;
use App\Models\TaskAssignee;

class TaskRepository
{
    public function find($id)
    {
        // dd($id);
        return Task::with(['attachments', 'assignees', 'users'])->where('tasks.id', $id)->first();
        // return TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.subject', 'tasks.project_id', 'tasks.start_date', 'tasks.priority_id', 'tasks.description')
        //     ->with(['task.attachments', 'task.assignees', 'task.users'])
        //     ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('task_assignees.task_id', $id)
        //     ->first();
    }


    public function findassignees($id)
    {

        // return Task::with(['attachments', 'assignees', 'users'])->where('tasks.id', $id)->first();   // dd($id);
        // return TaskAssignee::where('task_id', $id)->where('user_id', auth()->user()->id);
        return TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.subject', 'tasks.project_id', 'tasks.start_date', 'tasks.priority_id', 'tasks.description')
            ->with(['task.attachments', 'task.assignees', 'task.users'])
            ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.task_id', $id)
            // ->where('task_assignees.user_id', auth()->user()->id)
            ->first();

    }

    public function findassigneesAss($id)
    {

        // return Task::with(['attachments', 'assignees', 'users'])->where('tasks.id', $id)->first();   // dd($id);
        // return TaskAssignee::where('task_id', $id)->where('user_id', auth()->user()->id);
        return TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.subject', 'tasks.project_id', 'tasks.start_date', 'tasks.priority_id', 'tasks.description')
            ->with(['task.attachments', 'task.assignees', 'task.users'])
            ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.task_id', $id)
            ->where('task_assignees.user_id', auth()->user()->id)
            ->first();

    }

    public function findtaskrecuring($id)
    {
        // dd($id);
        return RecurringTask::where('id', $id)->first();
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($id, array $data)
    {
        // return Task::where('id', $id)->update($data);

        $task = Task::findOrFail($id);
        return $task->update($data);
    }

    public function updateTaskRecurring($id, array $data)
    {

        $task = RecurringTask::findOrFail($id);
        return $task->update($data);
    }

    public function updateAssigne($id, array $data)
    {
        // dd($id);
        // return Task::where('id', $id)->update($data);

        $task = TaskAssignee::where('task_id', $id)->where('user_id', auth()->user()->id)->first();
        // dd($task);
        return $task->update($data);
    }

    public function delete($id)
    {

        // return Task::where('id', $id)->delete();
        return Task::findOrFail($id)->delete();
    }

    public function deleteTaskrec($id)
    {
        RecurringTask::where('is_sub_task', $id)->delete();
        $task = RecurringTask::findOrFail($id);
        $task->delete();

        return $task;
    }
    public function getAll()
    {
        return Task::get();
    }
}
