<?php
namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function find($id)
    {
        return Task::with(['attachments', 'assignees', 'users'])->where('tasks.id', $id)->first();
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

    public function delete($id)
    {

        // return Task::where('id', $id)->delete();
        return Task::findOrFail($id)->delete();
    }
    public function getAll()
    {
        return Task::get();
    }
}
