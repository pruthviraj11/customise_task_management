<?php
namespace App\Services;

use App\Repositories\TaskRepository;

class TaskService
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    public function create($taskData)
    {
        $task = $this->taskRepository->create($taskData);
        return $task;
    }
    public function getAllTask()
    {
        $taskes = $this->taskRepository->getAll();
        return $taskes;
    }
    public function getTask($id)
    {
        $task = $this->taskRepository->find($id);
        return $task;
    }
    public function gettaskAssigne($id)
    {
        $task = $this->taskRepository->findassignees($id);
        return $task;
    }

    public function gettaskAssigneAss($id)
    {
        $task = $this->taskRepository->findassigneesAss($id);
        return $task;
    }

    public function gettaskrecuring($id)
    {
        // dd($id);
        $task = $this->taskRepository->findtaskrecuring($id);
        // dd($task);
        return $task;
    }
    public function deleteTask($id)
    {
        $deleted = $this->taskRepository->delete($id);
        return $deleted;
    }
    public function deleteTaskrec($id)
    {
        $deleted = $this->taskRepository->deleteTaskrec($id);
        return $deleted;
    }
    public function updateTask($id, $taskData)
    {
        $updated = $this->taskRepository->update($id, $taskData);
        return $updated;
    }
    public function updateTaskRecurring($id, $taskData)
    {
        $updated = $this->taskRepository->updateTaskRecurring($id, $taskData);
        return $updated;
    }
    public function updateTaskAssigne($id, $taskData)
    {
        $updated = $this->taskRepository->updateAssigne($id, $taskData);
        return $updated;
    }

}
