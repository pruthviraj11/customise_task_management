<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Task Created</title>
</head>

<body>
    <h1>New Task Created</h1>
    <p>Task Number: {{ $task->id }}</p>
    <p>A new task has been created:</p>
    <p>Title: {{ $task->title }}</p>
    <p>Description: {{ $task->description }}</p>
    <p><a href="{{ route('app-task-accept', $task->encryptedId) }}"
            style="display: inline-block; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; color: #fff; background-color: #17a2b8; border: 1px solid #17a2b8; border-radius: 0.25rem; text-decoration: none;">Accept
            the task</a></p>
    <p><a href="{{ route('app-task-get-requested_me') }}"
            style="display: inline-block; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; color: #007bff; background-color: transparent; border: 1px solid #007bff; border-radius: 0.25rem; text-decoration: none;">View
            the task</a></p>

</body>

</html>
