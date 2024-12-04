<?php
use App\Models\User;
use App\Models\TaskAssignee;
use Illuminate\Support\Facades\Auth;

function getAllTasksForHierarchy()
{
    $loggedInUserId = Auth::user()->id;
    $allUsers = [];
    $allTasks = [];

    // Retrieve the details of the logged-in user
    $loggedInUserDetails = User::find($loggedInUserId);
    $allUsers[$loggedInUserId] = $loggedInUserDetails;

    // Array to keep track of added user IDs
    $addedUserIds = [$loggedInUserId];

    // Function to recursively retrieve the hierarchy
    function getHierarchy($userId, &$allUsers, &$addedUserIds)
    {
        // Retrieve users reporting to the given user ID
        $reportingUsers = User::where('report_to', $userId)->get();

        foreach ($reportingUsers as $user) {
            if (!in_array($user->id, $addedUserIds)) {
                // Add the current user to the list of all users and mark its ID as added
                $allUsers[$user->id] = $user;
                $addedUserIds[] = $user->id;

                // Recursively retrieve the hierarchy of users reporting to the current user
                getHierarchy($user->id, $allUsers, $addedUserIds);
            }
        }
    }

    // Start retrieving the hierarchy from the logged-in user
    getHierarchy($loggedInUserId, $allUsers, $addedUserIds);

    // Retrieve tasks for all users in the hierarchy
    foreach ($addedUserIds as $userId) {
        $tasks = TaskAssignee::where('user_id', $userId)->get();
        foreach ($tasks as $task) {
            $allTasks[] = $task;
        }
    }

    return ['allTasks' => $allTasks];


}
