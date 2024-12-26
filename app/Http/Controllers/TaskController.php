<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\SubDepartment;
use App\Models\Status;
use App\Models\SubTask;
use App\Models\Priority;
use App\Models\Comments;
use App\Models\ReopenReason;
use App\Models\RecursiveTaskAttachment;
use App\Models\TaskAttachment;
use App\Models\RecurringTask;
use App\Models\TaskAssignee;
use Illuminate\Support\Facades\Storage;
use App\Services\RoleService;
use App\Services\TaskService;
use App\Services\StatusService;
use Psy\Util\Str;
use Spatie\Permission\Models\Permission;
use App\Mail\TaskCreatedMail;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Exports\TotalTasksExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\TaskUpdateImport;
class TaskController extends Controller
{
    protected TaskService $taskService;
    protected StatusService $statusService;

    public function __construct(TaskService $taskService, StatusService $statusService)
    {
        $this->taskService = $taskService;
        $this->statusService = $statusService;
        // $this->roleService = $roleService;
        $this->middleware('permission:task-list|task-create|task-edit|task-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:task-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:task-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:task-delete', ['only' => ['destroy']]);
        // Permission::create(['name' => 'activity', 'guard_name' => 'web', 'module_name' => 'Activity']);

        // Permission::create(['name' => 'task-list', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-create', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-edit', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-delete', 'guard_name' => 'web', 'module_name' => 'Task']);

    }

    public function index($user_id = '', $status_id = '', $route_type = '')
    {
        // dd($user_id);
        $tasks = Task::withTrashed()->get();

        foreach ($tasks as $task) {
            // Check if TaskNumber is null
            if (is_null($task->TaskNumber)) {
                // Assign TaskNumber
                $task->TaskNumber = $task->id;
                // Save the changes
                $task->save();
            }
        }

        $type = last(explode('-', request()->route()->getName()));
        $data['total_department'] = Task::count();
        $data['department'] = Task::get();


        // $taskAssignees = TaskAssignee::all();
        // foreach ($taskAssignees as $taskAssignee) {
        //     // Get the task by task_id from task_assignees, ensuring created_by is not null
        //     $task = Task::whereNotNull('task_status')
        //         ->whereNotNull('created_by') // Ensure created_by is not null in the tasks table
        //         ->where('id', $taskAssignee->task_id)
        //         ->first();

        //     if ($task != null) {
        //         // Check if task_status in task_assignees is null
        //         if ($task->task_status) {
        //             // Update the task_status in task_assignees from tasks table
        //             $taskAssignee->task_status = $task->task_status;
        //         }

        //         // Check if created_by in task_assignees is null
        //         if ($taskAssignee->created_by === null) {
        //             // Update the created_by in task_assignees from tasks table
        //             $taskAssignee->created_by = $task->created_by;
        //         }

        //         if ($taskAssignee->task_number === null) {
        //             // Get all task_assignees for the same task_id, ordered by user_id
        //             $existingTaskAssignees = TaskAssignee::where('task_id', $taskAssignee->task_id)
        //                 ->orderBy('user_id') // Or any other field to order by (e.g., task_id, created_at, etc.)
        //                 ->get();

        //             // Get the index of the current taskAssignee in the ordered list
        //             $userIndex = $existingTaskAssignees->search(function ($item) use ($taskAssignee) {
        //                 return $item->id === $taskAssignee->id;
        //             });

        //             // Increment task number based on the user's position in the task_assignees list
        //             $newTaskNumber = $userIndex + 1; // Indexing starts from 0, so we add 1 to make it start from 1

        //             // Generate a task number (task_id + userIndex), padding it to 2 digits
        //             $taskNumber = $taskAssignee->task_id . '-' . str_pad($newTaskNumber, 2, '0', STR_PAD_LEFT);

        //             // Update the task number for the current taskAssignee
        //             $taskAssignee->task_number = $taskNumber;

        //             // Save the updated taskAssignee
        //             $taskAssignee->save();
        //         }
        //         if ($taskAssignee->due_date == null) {
        //             // Store the task due_date on task_assignee
        //             $taskAssignee->due_date = $task->due_date;

        //             $taskAssignee->save();
        //         }
        //         if ($taskAssignee->accepted_date == null) {
        //             // Store the task accepted_date on task_assignee
        //             $taskAssignee->accepted_date = $task->accepted_date;

        //             $taskAssignee->save();
        //         }
        //         $user = User::find($taskAssignee->user_id);



        //         if ($user != null) {
        //             // Check if department and sub_department are null, and if so, update them
        //             if ($taskAssignee->department === null && $taskAssignee->sub_department === null) {
        //                 // Update department and sub_department from the user table
        //                 $taskAssignee->department = $user->department_id;
        //                 $taskAssignee->sub_department = $user->subdepartment;

        //                 // Save the taskAssignee with the updated department and sub_department
        //                 $taskAssignee->save();
        //             }
        //         }
        //         // Save the changes
        //         $taskAssignee->save();
        //     }
        // }

        return view('content.apps.task.list', compact('data', 'type', 'user_id', 'status_id', 'route_type'));
    }

    public function updateTaskNumber(Request $request)
    {
        $tasks = Task::withTrashed()->get();

        foreach ($tasks as $task) {
            // Check if TaskNumber is null
            if (is_null($task->TaskNumber)) {
                // Assign TaskNumber
                $task->TaskNumber = $task->id;
                // Save the changes
                $task->save();
            }
        }

        $tasks = RecurringTask::withTrashed()->get();

        foreach ($tasks as $task) {
            // Check if TaskNumber is null
            if (is_null($task->TaskNumber)) {
                // Assign TaskNumber
                $task->TaskNumber = $task->id;
                // Save the changes
                $task->save();
            }
        }

    }

    public function view($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $task = $this->taskService->gettask($id);
            if ($task && $task->creator->id == auth()->user()->id) {
                // dd($task);
                $creator = 1;
                $taskAssigne = $this->taskService->gettask($id);
                $getTaskComments = Comments::where('task_id', $task->id)->get();
                // $getTaskComments = Task::where('id', $task->task_id)->first();
            } else {
                $taskAssigne = $this->taskService->gettask($id);
                $task = $this->taskService->gettaskAssigne($id)->first();
                $getTaskComments = Comments::where('task_id', $task->task_id)->first();
                // dd($getTaskComments);
                $creator = 0;
                // dd($task);
            }
            $page_data['page_title'] = "Task";
            $page_data['form_title'] = "Edit Task";

            $projects = Project::where('status', 'on')->get();
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $Status = Status::where('status', 'on')->get();
            $Prioritys = Priority::where('status', 'on')->get();
            $users = User::with('department')->where('status', '1')->get();
            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;
            $user = auth()->user();
            $hasAcceptedTask = false;
            if ($user) {
                $hasAcceptedTask = $task->isAcceptedByUser($user->id);
            }
            return view('content.apps.task.view', compact('page_data', 'hasAcceptedTask', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'getTaskComments', 'taskAssigne', 'creator'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }

    }

    public function recview($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            // dd($id);


            $task = RecurringTask::where('id', $id)->first();
            // dd($task);
            $creator = 1;

            $assignedUserIds = explode(',', $task->task_assignes);


            $page_data['page_title'] = "Task";
            $page_data['form_title'] = "Edit Task";

            $projects = Project::where('status', 'on')->get();
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $Status = Status::where('status', 'on')->get();
            $Prioritys = Priority::where('status', 'on')->get();
            //            dd($task->department_id);
            $users = User::where('status', '1')
                ->where('id', '!=', 1)
                ->get();


            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;
            // dd($creator);

            return view('content.apps.task.recview', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'assignedUserIds'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }

    }

    public function kanban($type = null)
    {

        $pageConfigs = [
            'pageHeader' => true,
            'pageClass' => 'kanban-application',
        ];
        // $type = last(explode('-', request()->route()->getName()));
        // echo $type;
        // die;
        $data['total_department'] = Task::count();
        $data['department'] = Task::get();
        return view('content.apps.task.kanban', compact('data', 'type', 'pageConfigs'));
    }

    public function getAll()
    {
        if (auth()->user()->id == 1 || auth()->user()->hasRole('Super Admin')) {
            $tasks = $this->taskService->getAlltask();
        } else {
            $tasks = Task::select('tasks.*')
                ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                ->where('task_assignees.status', 1)
                ->where('task_assignees.user_id', auth()->user()->id);
        }

        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm me-1 '  href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;

            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }

        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "";

            })->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";

            })

            ->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;

            })->rawColumns(['actions'])->make(true);

    }
    /*25-10
        public function getAll_mytask()
        {
            // dd('jklhsdfdsf');
            $userId = auth()->user()->id;

            // Retrieve tasks where the user is either the creator or assigned
            $tasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                ->where(function ($query) use ($userId) {
                    $query->where('tasks.created_by', $userId)
                        ->Where('task_assignees.user_id', $userId);
                })
                ->where('task_assignees.status', '!=', 2); // Exclude status 2


            return DataTables::of($tasks)->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

                // Delete Button
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
                return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
            })->addColumn('created_by_username', function ($row) {
                if ($row->creator) {
                    return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('id', function ($row) {
                return $row->id;
            })
                ->addColumn('task_Assign', function ($row) {
                    // Get all names assigned to this task
                    if ($row->users) {
                        return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
                    } else {
                        return "-";
                    }
                })->addColumn('task_status_name', function ($row) {
                    return $row->taskStatus->status_name ?? "-";
                })
                ->addColumn('project_name', function ($row) {
                    return $row->project->project_name ?? "-";
                })
                ->addColumn('department_name', function ($row) {
                    return $row->department->department_name ?? "-";
                })
                ->addColumn('sub_department_name', function ($row) {

                    return $row->sub_department->sub_department_name ?? "-";
                })->addColumn('created_by_department', function ($row) {
                    if ($row->creator && $row->creator->department) {
                        return $row->creator->department->department_name ?? '-';
                    } else {
                        return "-";
                    }
                })->addColumn('created_by_sub_department', function ($row) {
                    if ($row->creator && $row->creator->sub_department) {
                        return $row->creator->sub_department->sub_department_name ?? '-';
                    } else {
                        return "-";
                    }
                })->addColumn('created_by_phone_no', function ($row) {
                    if ($row->creator && $row->creator->phone_no) {
                        return $row->creator->phone_no ?? '-';
                    } else {
                        return "-";
                    }
                })->addColumn('description', function ($row) {
                    $description = html_entity_decode($row->description);
                    return $description;
                })->rawColumns(['actions'])->make(true);
        }
    25-10*/
    public function getAll_my_total()
    {
        // dd('jklhsdfdsf');
        $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $tasksCreatedByUser = Task::where(function ($query) use ($userId) {
            $query->where('created_by', $userId)
                ->whereHas('assignees', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
        })
            ->whereHas('assignees', function ($query) {
                $query->where('status', 1);
            });

        $tasksAssignedToUser = Task::select('tasks.*')
            ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.status', 1)
            ->where('tasks.created_by', '!=', $userId);

        $tasks = $tasksCreatedByUser->union($tasksAssignedToUser);
        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete btn-sm' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;


            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function storeComments(Request $request)
    {

        if ($request->comment_form != '') {
            // Create a new comment
            $comment = new Comments();
            $comment->comment = $request->get('comment_form');
            $comment->task_id = $request->get('task_id');
            $comment->created_by = auth()->id();

            // Check if 'comments_for' is empty or null
            if (empty($request->comments_for)) {
                // If 'comments_for' is empty or null, store task creator's ID in 'to_user_id'
                $comment->to_user_id = $request->task_created_by;
            } else {
                // Otherwise, store the comma-separated list of user IDs in 'to_user_id'
                $comment->to_user_id = implode(',', $request->comments_for);
            }
            // dd($comment);
            // Save the comment
            $comment->save();
        }
        return redirect()->back()->with('success', 'Comment added successfully!');
    }
    public function getAll_accepted_by_me(Request $request)
    {
        // dd('sdf');
        // $userId = auth()->user()->id;
        $user = auth()->user();

        // Retrieve tasks where the user is either the creator or assigned
        // $tasks = Task::select('tasks.*')->leftjoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('task_assignees.status', 1)
        //     ->where('tasks.created_by', '!=', $userId);
        $tasks = Task::whereHas('assignees', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', '1');
        })->whereNotIn('created_by', [$user->id]);
        if (auth()->user()->id == 1 || auth()->user()->hasRole('Super Admin')) {
            $tasks = Task::whereHas('assignees', function ($query) use ($user) {
                $query->where('status', '1');
            })->whereNotIn('created_by', [$user->id]);
        }

        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete btn-sm mx-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task

            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    // public function getAll_assign_by_me(Request $request)
    // {
    //     $userId = auth()->user()->id;

    //     $tasks = Task::where('created_by', $userId)
    //         ->whereDoesntHave('assignees', function ($query) use ($userId) {
    //             $query->where('user_id', $userId);
    //         });

    //     if (!empty($request->search['value'])) {
    //         // $tasks = Task::query();
    //         $searchTerm = $request->search['value'];
    //         $tasks->where(function ($query) use ($searchTerm) {
    //             $query->where('TaskNumber', 'like', '%' . $searchTerm . '%')
    //                 ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
    //                 ->orWhere('title', 'like', '%' . $searchTerm . '%');
    //             // Add other columns as needed
    //         });
    //     }
    //     // dd($tasks);
    //     return DataTables::of($tasks)->addColumn('actions', function ($row) {
    //         $encryptedId = encrypt($row->task_id);
    //         // Update Button
    //         $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

    //         // Delete Button
    //         $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

    //         $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
    //         $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
    //         return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
    //     })  ->addColumn('created_by_username', function ($row) {
    //             return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
    //         })
    //         ->addColumn('Task_number', function ($row) {
    //             return $row->task_number ??  "-";
    //         })
    //         ->addColumn('Task_Ticket', function ($row) {
    //             return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
    //         })
    //         ->addColumn('description', function ($row) {
    //             return $row->task && $row->task->description ? $row->task->description : '-';
    //         })

    //         ->addColumn('subject', function ($row) {
    //             return $row->task && $row->task->subject ? $row->task->subject : '-';
    //         })
    //         ->addColumn('title', function ($row) {
    //             return $row->task && $row->task->title ? $row->task->title : '-';
    //         })
    //         ->addColumn('Task_assign_to', function ($row) {
    //             return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
    //         })

    //         ->addColumn('status', function ($row) {
    //             return $row->task_status ? $row->taskStatus->status_name : "-";
    //         })
    //         ->addColumn('Created_Date', function ($row) {
    //             return $row->task && $row->task->created_at ? $row->task->created_at : '-';
    //         })
    //         ->addColumn('start_date', function ($row) {
    //             return $row->task && $row->task->start_date ? $row->task->start_date : '-';
    //         })
    //         ->addColumn('due_date', function ($row) {
    //             return $row->task && $row->task->due_date ? $row->task->due_date : '-';
    //         })
    //         ->addColumn('close_date', function ($row) {
    //             return $row->task && $row->task->close_date ? $row->task->close_date : '-';
    //         })
    //         ->addColumn('completed_date', function ($row) {
    //             return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
    //         })
    //         ->addColumn('accepted_date', function ($row) {
    //             return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
    //         })

    //         ->addColumn('project', function ($row) {
    //             return $row->task && $row->task->project ? $row->task->project->project_name : '-';
    //         })
    //         ->addColumn('department', function ($row) {
    //             return $row->task && $row->task->department ? $row->task->department->department_name : '-';
    //         })

    //         ->addColumn('sub_department', function ($row) {
    //             return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
    //         })
    //         ->addColumn('creator_department', function ($row) {
    //             return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
    //         })

    //         ->addColumn('creator_sub_department', function ($row) {
    //             return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
    //         })
    //         ->addColumn('creator_phone', function ($row) {
    //             return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
    //         })->rawColumns(['actions'])->make(true);
    // }

    public function getAll_assign_by_me(Request $request)
    {
        // dd($request->search['value']);
        $userId = auth()->user()->id;

        // Fetch tasks assigned to the user but created by the authenticated user
        $tasks = TaskAssignee::with(['task', 'creator', 'department_data', 'sub_department_data'])->select('task_assignees.*', 'tasks.title', 'tasks.description', 'tasks.subject')
            ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.created_by', $userId)
            ->whereDoesntHave('user', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });



        if ($request->has('search') && $request->get('search')['value']) {
            $search = $request->get('search')['value'];

            $tasks->where(function ($query) use ($search) {
                $query->where('tasks.title', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.description', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.subject', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.task_number', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.remark', 'LIKE', "%{$search}%")
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('department_data', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('department_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('sub_department_data', function ($subDepartmentQuery) use ($search) {
                        $subDepartmentQuery->where('sub_department_name', 'LIKE', "%{$search}%");
                    });
            });
        }
        return DataTables::of($tasks->get())
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }

                // Update Button
                // $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                // // Delete Button
                // $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                $viewButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='View Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                $buttons = $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewButton;
                return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
            })
            ->addColumn('created_by_username', function ($row): string {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
            })
            ->addColumn('description', function ($row) {
                return ($row->task && $row->task->description) ? $row->task->description : '-';
            })
            ->addColumn('subject', function ($row) {
                return ($row->task && $row->task->subject) ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return ($row->task && $row->task->title) ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })
            ->addColumn('status', function ($row) {
                return ($row->task && $row->task->task_status) ? $row->task->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return ($row->task && $row->task->created_at) ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return ($row->task && $row->task->start_date) ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })
            ->addColumn('close_date', function ($row) {
                return ($row->task && $row->task->close_date) ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return ($row->task && $row->task->completed_date) ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })
            ->addColumn('project', function ($row) {
                return ($row->task && $row->task->project) ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return ($row->department && $row->department_data) ? $row->department_data->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return ($row->sub_department && $row->sub_department_data) ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return ($row->creator && $row->creator->department) ? $row->creator->department->department_name : '-';
            })
            ->addColumn('creator_sub_department', function ($row) {
                return ($row->creator && $row->creator->sub_department) ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return ($row->creator && $row->creator->phone_no) ? $row->creator->phone_no : '-';
            })
            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);

    }

    public function getAll_requested_me()
    {
        $userId = auth()->user()->id;


        // $tasks = DB::table('tasks')
        //     ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('task_assignees.user_id', '=', $userId)
        //     ->where('task_assignees.status', '=', 0)
        //     ->where('tasks.created_by', '!=', $userId);
        $tasks = Task::select('tasks.*')->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $userId)
            ->where('task_assignees.status', 0)
            ->where('tasks.created_by', '!=', $userId);

        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            return $row->creator->first_name . " " . $row->creator->last_name ?? '-'; // This should work fine now
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            $assignedNames = $row->users->map(function ($user) {
                return $user->first_name . ' ' . $user->last_name;
            })->implode(', ');

            return $assignedNames ?? '-';
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name;
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name;
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name;
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAllForView($type = null)
    {
        // dd($type);
        $status = $this->statusService->getAllstatus();
        $tasks = $this->taskService->getAlltask()->toArray();

        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }
    public function getAll_kanban_total_task()
    {
        $status = $this->statusService->getAllstatus();
        $userId = auth()->user()->id;

        $userId = auth()->user()->id;
        $user = auth()->user();
        $tasks = [];
        ini_set('memory_limit', '2048M');

        // Function to recursively retrieve the hierarchy
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

        $query = Task::query();



        if ($userId == 1 || auth()->user()->hasRole('Super Admin')) {
            $query->where('task_status', '!=', 2);
        } else {
            $query = Task::query();

            $query->where(function ($query) use ($addedUserIds) {
                $query->whereIn('created_by', $addedUserIds)
                    ->orWhereHas('assignees', function ($q) use ($addedUserIds) {
                        $q->whereIn('user_id', $addedUserIds);
                    });
            });

        }

        $tasks = $query->select('tasks.*')->get();


        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }
    public function getAll_kanban_mytask()
    {
        // dd('zdf');
        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $tasks = Task::where(function ($query) use ($userId) {
            $query->where('created_by', $userId)
                ->WhereHas('assignees', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
        })
            ->whereHas('assignees', function ($query) {
                $query->where('status', 1);
            })->get();
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }

    public function getAll_kanban_main()
    {
        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;

        if (Auth()->user()->id == 1) {
            $tasks = Task::whereNull('deleted_at')->get();
        } else {
            $tasks = Task::where('created_by', $userId)->whereNull('deleted_at')->get();

        }


        // $tasks = TaskAssignee::with(['task', 'creator', 'department_data', 'sub_department_data'])->select('task_assignees.*', 'tasks.title', 'tasks.description', 'tasks.subject')
        //     ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
        //     ->where('task_assignees.created_by', $userId)
        //     ->whereDoesntHave('user', function ($query) use ($userId) {
        //         $query->where('user_id', $userId);
        //     })->get();
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }

    public function getAll_kanban_dueDatePast()
    {

        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;


        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title')->whereNotIn('task_status', ['4', '7'])->where('due_date', '<', today())
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->get();
        } else {
            // Retrieve tasks where the user is either the creator or assigned
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.id as id_task')->where('task_assignees.status', 1)->where('task_assignees.due_date', '<', today())
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->whereNotIn('task_assignees.task_status', ['4', '7'])
                ->where(function ($q) use ($userId) {
                    $q->where('task_assignees.user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('task_assignees.deleted_at');
                        });
                })->get();
        }
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id_task']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }


        return response()->json($res);

    }

    public function getAll_kanban_pendingTask()
    {

        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;


        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.id as id_task')
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->whereNotIn('task_assignees.task_status', ['4', '7'])->get();
        } else {
            // Retrieve tasks where the user is either the creator or assigned
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.id as id_task')->whereNotIn('task_assignees.task_status', ['4', '7'])
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->where(function ($q) use ($userId) {
                    $q->where('task_assignees.user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('task_assignees.deleted_at');
                        });
                })->get();
        }
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id_task']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }

    public function getAll_kanban_completedTask()
    {

        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;

        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.id as id_task')
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->whereIn('task_assignees.task_status', ['4','7'])->get();
        } else {
            // Retrieve tasks where the user is either the creator or assigned
            $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.id as id_task')->whereIn('task_assignees.task_status', ['4','7'])
                ->leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
                ->where(function ($q) use ($userId) {
                    $q->where('task_assignees.user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('task_assignees.deleted_at');
                        });
                })->get();
        }
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id_task']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }

    public function getAll_kanban_assign_by_me()
    {
        // dd('zdf');
        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $tasks = Task::where('created_by', $userId)
            ->whereDoesntHave('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }
    public function getAll_kanban_accepted()
    {
        // dd('zdf');
        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        // $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $user = auth()->user();

        // $tasks = $user->tasks()->where('status', '1');
        $tasks = Task::whereHas('assignees', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', '1');
        })
            ->get();
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }
    public function getAll_kanban_requested()
    {
        // dd('zdf');
        $status = $this->statusService->getAllstatus();
        // $tasks = $this->taskService->getAlltask()->toArray();
        $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $user = auth()->user();

        // $tasks = $user->tasks()->where('status', '1');
        if (Auth()->user()->id == 1 || auth()->user()->hasRole('Super Admin')) {
            $tasks = Task::whereHas('assignees', function ($query) {
                $query->where('status', 0);
            })->get();
        } else {
            // $tasks = $user->tasks()->where('status', '0');
            $tasks = Task::whereHas('assignees', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', '0');
            })
                ->get();
        }
        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }
    public function getAll_kanban_all()
    {
        $status = $this->statusService->getAllstatus();
        $userId = auth()->user()->id;
        $user = auth()->user();

        if (auth()->user()->id == 1 || auth()->user()->hasRole('Super Admin')) {
            $tasks = $this->taskService->getAlltask();
        } else {
            $tasks = Task::select('tasks.*')
                ->leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                ->where('task_assignees.status', 1)
                ->where('task_assignees.user_id', auth()->user()->id);
        }


        // dd($tasks);
        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            // dd($key, $item);
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => $item['task_status'],
                "badge" => "success",
                "due-date" => date('d F', strtotime($item['due_date'])),
                "attachments" => "0",
                "assigned" => [
                    "avatar-s-1.jpg",
                    "avatar-s-2.jpg"
                ],
                "members" => ["Bruce", "Dianna"]
            ];
        }

        $res = [];
        foreach ($status as $key => $value) {
            $res[] = ['id' => encrypt($value['id']), 'title' => $value['displayname'], 'item' => (isset($tasksTemp[$value['id']])) ? $tasksTemp[$value['id']] : []];
        }

        return response()->json($res);

    }


    public function getAll_main(Request $request)
    {
        $user = auth()->user();

        $query = Task::query();

        if (Auth()->user()->id == 1) {

            // $tasks = TaskAssignee::whereHas('task', function ($query) {

            //     // $query->whereHas('assignees', function ($query) {
            //     $query->where('status', 0)
            //         // })
            //         ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            // })
            //     ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
            //     ->get();
            $query->whereNull('deleted_at')->get();
        } else {

            // $tasks = TaskAssignee::whereHas('task', function ($query) use ($user) {

            //     // $query->whereHas('assignees', function ($query) use ($user) {
            //     $query->where('user_id', $user->id)->where('status', 0)
            //         // })
            //         ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            // })
            //     ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
            //     ->get();

            $query->where('created_by', $user->id)->whereNull('deleted_at')->get();

        }


        if ($task_filter = $request->input('task')) {
            // Assuming you want to filter by 'ticket' column in the 'tasks' table, make sure you join the tasks table
            $query->where('ticket', $task_filter);

        }

        if ($department_filter = $request->input('department')) {
            $query->whereHas('taskAssignees', function ($q) use ($department_filter) {
                $q->where('department', $department_filter);
            });
        }

        if ($created_by = $request->input('created_by')) {
            $query->whereHas('taskAssignees', function ($q) use ($created_by) {
                $q->where('created_by', $created_by);
            });
        }

        if ($assignees = $request->input('assignees')) {
            $query->whereHas('users', function ($q) use ($assignees) {
                $q->whereIn('user_id', $assignees);
            });
        }

        if ($status = $request->input('status')) {
            $query->whereHas('taskAssignees', function ($q) use ($status) {
                $q->where('task_status', $status);
            });
        }

        // Date filters
        if ($request->input('dt_date')) {
            $dtDateRange = parseDateRange($request->input('dt_date'));

            // $query->whereHas('task', function ($q) use ($task_filter, $dtDateRange, $request) {
            if (!empty($dtDateRange[1])) {
                // Both start and end dates are available
                $query->whereBetween('start_date', [$dtDateRange[0], $dtDateRange[1]]);
            } else {
                $inputDate = $request->input('dt_date');
                $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                // Only a single date is provided
                $query->whereDate('start_date', $formattedDate);
            }
            // });
        }



        if ($request->input('accepted_task_date')) {
            $dtDateRange = parseDateRange($request->input('accepted_task_date'));
            // $query->whereHas('task', function ($q) use ($task_filter, $dtDateRange, $request) {
            if (!empty($dtDateRange[1])) {
                // Both start and end dates are available
                $query->whereBetween('accepted_date', [$dtDateRange[0], $dtDateRange[1]]);
            } else {
                $inputDate = $request->input('accepted_task_date');
                $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                // Only a single date is provided
                $query->whereDate('accepted_date', $formattedDate);
            }
            // });
        }

        if ($request->input('end_date')) {
            $dtDateRange = parseDateRange($request->input('end_date'));
            if (!empty($dtDateRange[1])) {
                // Both start and end dates are available
                $query->whereBetween('due_date', [$dtDateRange[0], $dtDateRange[1]]);
            } else {
                $inputDate = $request->input('end_date');
                $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                // Only a single date is provided
                $query->whereDate('due_date', $formattedDate);
            }
        }

        // Handle the project filter
        if ($project = $request->input('project')) {
            $query->where('project_id', $project);
        }

        $tasks = $query->get();
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // $satusData = TaskAssignee::where('')
            $updateButton = '';
            $deleteButton = '';
            $acceptButton = '';
            if (auth()->user()->id == '1') {
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            }
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

            return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
        })
            ->addColumn('task_id', function ($row) {
                return $row->id ?? "-";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->TaskNumber ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->ticket == 0 ? 'Task' : 'Ticket';
            })
            ->addColumn('description', function ($row) {
                return $row->description ?? '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->subject ?? '-';
            })
            ->addColumn('title', function ($row) {
                return $row->title ?? '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                // return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";

                $data = TaskAssignee::where('task_id', $row->id)->get();
                // Get the user names as a comma-separated string
                $userNames = $data->map(function ($assignee) {
                    return $assignee->user ? $assignee->user->first_name . " " . $assignee->user->last_name : null;
                })->filter()->implode(', ');

                // Return the comma-separated user names
                return $userNames ?: '-';
            })
            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->close_date ? Carbon::parse($row->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->completed_date ? Carbon::parse($row->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->project ? $row->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department ? $row->department->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department ? $row->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }


    public function getAll_recurring_main(Request $request)
    {
        // dd("sdsd");
        $user = auth()->user();

        $query = RecurringTask::query();
        // dd($query->get());
        // Filter only those tasks where is_sub_task is null
        $query->whereNull('is_sub_task');

        if (Auth()->user()->id == 1) {

            // $tasks = TaskAssignee::whereHas('task', function ($query) {

            //     // $query->whereHas('assignees', function ($query) {
            //     $query->where('status', 0)
            //         // })
            //         ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            // })
            //     ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
            //     ->get();
            $query->whereNull('deleted_at')->get();

        } else {

            // $tasks = TaskAssignee::whereHas('task', function ($query) use ($user) {

            //     // $query->whereHas('assignees', function ($query) use ($user) {
            //     $query->where('user_id', $user->id)->where('status', 0)
            //         // })
            //         ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            // })
            //     ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
            //     ->get();

            $query->where('created_by', $user->id)->whereNull('deleted_at')->get();

        }




        $tasks = $query->get();
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // $satusData = TaskAssignee::where('')
            $updateButton = '';
            $deleteButton = '';
            $acceptButton = '';
            if (auth()->user()->id == '1') {
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-recurringedit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-recurring_destroy' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-recurring_destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-recurringedit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-recurring_destroy' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-recurring_destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            }
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-recview', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

            return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
        })
            ->addColumn('task_id', function ($row) {
                return $row->id ?? "-";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->TaskNumber ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->ticket == 0 ? 'Task' : 'Ticket';
            })
            ->addColumn('description', function ($row) {
                return $row->description ?? '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->subject ?? '-';
            })
            ->addColumn('title', function ($row) {
                return $row->title ?? '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                // Split the task_assignes field into individual user IDs
                $temps = explode(',', $row->task_assignes);
                // Initialize an empty array to store usernames
                $usernames = [];

                // Loop through each user ID
                foreach ($temps as $temp) {
                    // Fetch the user data using the user ID
                    $usernamedata = User::where('id', $temp)->first();
                    if ($usernamedata) {
                        // Concatenate first and last name and add to the usernames array
                        $usernames[] = $usernamedata->first_name . " " . $usernamedata->last_name;
                    }
                }

                // Join the usernames with commas and return the result, or return '-' if no usernames are found
                return !empty($usernames) ? implode(', ', $usernames) : '-';
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->close_date ? Carbon::parse($row->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->completed_date ? Carbon::parse($row->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->project ? $row->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department ? $row->department->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department ? $row->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function getAll_dueDatePast(Request $request)
    {
        // dd('Hii');
        $userId = Auth()->user()->id;
        ini_set('memory_limit', '2048M'); // Retain memory limit increase, but we'll use chunking to minimize memory usage

        // Common query for all tasks
        $query = TaskAssignee::query();
        // dd(today());

        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $query->whereNotIn('task_status', ['4', '7'])->where('due_date', '<', today());
        } else {
            // User-specific task filters
            $query->where('status', 1)->where('due_date', '<', today())
                ->whereNotIn('task_status', ['4', '7'])
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('deleted_at');
                        });
                });
        }

        $tasks = $query->get();
        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";

                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }


    public function getAll_pendingTask(Request $request)
    {
        $userId = Auth()->user()->id;
        ini_set('memory_limit', '2048M'); // Retain memory limit increase, but we'll use chunking to minimize memory usage

        // Common query for all tasks
        $query = TaskAssignee::query();
        // dd(today());

        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $query->whereNotIn('task_status', ['4', '7']);
        } else {
            // User-specific task filters
            $query->whereNotIn('task_status', ['4', '7'])
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('deleted_at');
                        });
                });
        }

        $tasks = $query->get();
        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";

                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function getAll_completedTask(Request $request)
    {
        $userId = Auth()->user()->id;
        ini_set('memory_limit', '2048M'); // Retain memory limit increase, but we'll use chunking to minimize memory usage

        // Common query for all tasks
        $query = TaskAssignee::query();
        // dd(today());

        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $query->whereIn('task_status', ['4','7']);
        } else {
            // User-specific task filters
            $query->whereIn('task_status', ['4','7'])
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereHas('user', function ($q) {
                            // Ensure the user is not deleted (i.e., deleted_at is null)
                            $q->whereNull('deleted_at');
                        });
                });
        }

        $tasks = $query->get();
        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";

                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function getAll_requested()
    {
        $user = auth()->user();

        if (Auth()->user()->id == 1) {

            $tasks = TaskAssignee::whereHas('task', function ($query) {

                // $query->whereHas('assignees', function ($query) {
                $query->where('status', 0)
                    // })
                    ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            })
                ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
                ->get();
        } else {

            $tasks = TaskAssignee::whereHas('task', function ($query) use ($user) {

                // $query->whereHas('assignees', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 0)
                    // })
                    ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            })
                ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
                ->get();
        }


        return DataTables::of($tasks)->addColumn('actions', function ($row) {


            $encryptedId = encrypt($row->task->id);

            // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
            $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";


            $rejectButton = "<a href='#' class='btn-sm  btn-danger btn-sm me-1 reject-btn' data-bs-toggle='tooltip' data-bs-placement='top' title='Reject Task' data-id='$encryptedId' data-toggle='modal' data-target='#exampleModal'><i class='ficon' data-feather='x-circle'></i></a>";


            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";


            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";


            $buttons = " " . $acceptButton . "  " . $rejectButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
            })
            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })
            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function reject_task(Request $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $taskDetails = TaskAssignee::where('task_id', $id)->where('user_id', auth()->user()->id)->first();
            // dd($taskDetails);
            $createdById = $taskDetails->created_by;
            $departmentId = $taskDetails->department;

            $departmentDetails = Department::where('id', $departmentId)->first();
            // dd($departmentDetails);

            $userDetails = User::where('id', $createdById)->first();
            $departmentHOD = User::where('id', $departmentDetails->hod)->first();
            // dd($departmentHOD);

            $userId = auth()->user()->id;
            TaskAssignee::where('user_id', $userId)
                ->where('task_id', $id)
                ->update([
                    'status' => 2,
                    'remark' => $request->get('remark'),
                    'updated_at' => now(),
                ]);
            $userDetails = $userDetails->email;
            $hodMail = $departmentHOD->email;
            $subject = "Task Rejected";
            $html = View::make('emails.task_Rejected', compact('taskDetails'))->render();
            // Mail::to($userDetails)->send(new TaskCreatedMail($subject, $html));
            // Mail::to($hodMail)->send(new TaskCreatedMail($subject, $html));
            return redirect()->route("app-task-requested")->with('success', 'Task Rejected Successfully');
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-requested")->with('error', 'Error While Rejecting Task');
        }
    }

    public function getAll_accepted(Request $request)
    {

        $user = auth()->user();

        // Modify query based on task_assignees table
        if ($user->id == 1) {
            $tasks = TaskAssignee::with(['task', 'creator', 'department_data', 'sub_department_data'])->select('task_assignees.*', 'tasks.title', 'tasks.description', 'tasks.subject')
                ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
                ->whereHas('task', function ($query) {
                    $query->where('status', '1'); // Assuming 'status' is in the Task model
                });
        } else {
            $tasks = TaskAssignee::with(['task', 'creator', 'department_data', 'sub_department_data'])->select('task_assignees.*', 'tasks.title', 'tasks.description', 'tasks.subject')
                ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
                ->whereHas('task', function ($query) use ($user) {
                    $query->where('status', '1'); // Assuming 'status' is in the Task model
                })->where('user_id', $user->id); // Ensure we filter by the logged-in user
        }

        // // Apply search filter if provided
        // if (!empty($request->search['value'])) {
        //     $searchTerm = $request->search['value'];
        //     $tasks->where(function ($query) use ($searchTerm) {
        //         // $query->whereHas('task', function ($subQuery) use ($searchTerm) {
        //         $query->where('task_number', 'like', '%' . $searchTerm . '%');
        //                 // ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
        //                 // ->orWhere('title', 'like', '%' . $searchTerm . '%');
        //         // });
        //     });
        // }

        if ($request->has('search') && $request->get('search')['value']) {
            $search = $request->get('search')['value'];

            $tasks->where(function ($query) use ($search) {
                $query->where('tasks.title', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.description', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.subject', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.task_number', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.remark', 'LIKE', "%{$search}%")
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('department_data', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('department_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('sub_department_data', function ($subDepartmentQuery) use ($search) {
                        $subDepartmentQuery->where('sub_department_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        return DataTables::of($tasks->get())
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success accept-task me-1' data-idos='" . $encryptedId . "' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                // $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                // $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Delete Task' class='btn-sm btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                $viewButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='View Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                $buttons = $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewButton;
                return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
            })
            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })
            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })
            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->task->taskStatus->status_name : "-"; // Assuming 'task_status' is on the Task model
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })
            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })
            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }


    public function requestedToUsTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);
        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {

            $tasks = TaskAssignee::where('user_id', $user)->where('status', '0')->where('created_by', $user_id)->get();
        } elseif ($type == 'requested_by_me') {
            $tasks = TaskAssignee::where('user_id', $user_id)->where('status', '0')->where('created_by', $user)->get();
        } elseif ($type == 'total_task') {
            // Fetch the tasks for both tasks_A and tasks_B and combine them
            $tasks_A = TaskAssignee::where('user_id', $user)
                ->where('status', '0')
                ->where('created_by', $user_id)
                ->get();

            $tasks_B = TaskAssignee::where('user_id', $user_id)
                ->where('status', '0')
                ->where('created_by', $user)
                ->get();

            // Combine the results into one collection
            $tasks = $tasks_A->merge($tasks_B);
        }

        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }
    public function requestedToUsStatusTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);
        $user = auth()->user()->id;
        // dd($type);
        if ($type == 'requested_to_us') {
            $tasks = TaskAssignee::where('user_id', $user)->where('task_status', $status_id)->where('created_by', $user_id)->get();

        } elseif ($type == 'requested_by_me') {
            $tasks = TaskAssignee::where('user_id', $user_id)->where('task_status', $status_id)->where('created_by', $user)->get();

        } elseif ($type == 'total_task') {
            $tasks_A = TaskAssignee::where('user_id', $user)->where('task_status', $status_id)->where('created_by', $user_id)->get();

            $tasks_B = TaskAssignee::where('user_id', $user_id)->where('task_status', $status_id)->where('created_by', $user)->get();

            $tasks = $tasks_A->merge($tasks_B);

        }
        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function requestedToUsPendingTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);

        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {
            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 5, 6])
                ->where('created_by', $user_id)
                ->get();
        } elseif ($type == 'requested_by_me') {
            $tasks = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', [1, 3, 5, 6])
                ->where('created_by', $user)
                ->get();
        } elseif ($type == 'total_task') {
            $tasks_A = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 5, 6])
                ->where('created_by', $user_id)
                ->get();

            $tasks_B = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', [1, 3, 5, 6])
                ->where('created_by', $user)
                ->get();

            $tasks = $tasks_A->merge($tasks_B);

        }
        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }
    public function requestedToUsOverDuesTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);

        $user = auth()->user()->id;
        $cdate = date("Y-m-d");
        if ($type == 'requested_to_us') {
            // $due_tasks = $due_tasks = TaskAssignee::where('user_id', $user)
            //     ->where('created_by', $user_id)
            //     ->whereNotIn('task_status', [4, 7])
            //     ->get();
            // $tasksData = [];
            // foreach ($due_tasks as $due_task) {
            //     $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->get();
            //     foreach ($countTotalTask as $task) {
            //         $tasksData[] = $task; // Add the task to the array
            //     }
            // }
            $tasksData = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->get();

        } elseif ($type == 'requested_by_me') {
            // $due_tasks = $due_tasks = TaskAssignee::where('user_id', $user_id)
            //     ->where('created_by', $user)
            //     ->whereNotIn('task_status', [4, 7])
            //     ->get();
            // $tasksData = [];
            // foreach ($due_tasks as $due_task) {
            //     $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->get();
            //     foreach ($countTotalTask as $task) {
            //         $tasksData[] = $task; // Add the task to the array
            //     }
            // }

            $tasksData = TaskAssignee::where('user_id', $user_id)
                ->where('created_by', $user)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->get();
        } elseif ($type = 'total_task') {

            // $due_tasks_A = TaskAssignee::where('user_id', $user)
            //     ->where('created_by', $user_id)
            //     ->whereNotIn('task_status', [4, 7])
            //     ->get();

            // $due_tasks_B = TaskAssignee::where('user_id', $user_id)
            //     ->where('created_by', $user)
            //     ->whereNotIn('task_status', [4, 7])
            //     ->get();

            $due_tasks_A = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->get();

            $due_tasks_B = TaskAssignee::where('user_id', $user_id)
                ->where('created_by', $user)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '<', $cdate)
                ->get();

            $tasksData = $due_tasks_A->merge($due_tasks_B);

            // $tasksData = [];
            // foreach ($merged_due_tasks as $due_task) {
            //     $countTotalTask = Task::where('id', $due_task->task_id)
            //         ->whereDate('due_date', '<', $cdate)
            //         ->get();

            //     foreach ($countTotalTask as $task) {
            //         $tasksData[] = $task; // Add the task to the array
            //     }
            // }
        }
        return DataTables::of($tasksData)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function requestedToUsTodayDuesTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);

        $user = auth()->user()->id;
        $cdate = date("Y-m-d");
        if ($type == 'requested_to_us') {

            $tasksData = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '=', $cdate)
                ->get();

        } elseif ($type == 'requested_by_me') {

            $tasksData = TaskAssignee::where('user_id', $user_id)
                ->where('created_by', $user)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '=', $cdate)
                ->get();

        } elseif ($type == 'total_task') {
            $today_tasks_A = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '=', $cdate)
                ->get();


            $today_tasks_B = TaskAssignee::where('user_id', $user_id)
                ->where('created_by', $user)
                ->whereNotIn('task_status', [4, 7])
                ->whereDate('due_date', '=', $cdate)
                ->get();

            $tasksData = $today_tasks_A->merge($today_tasks_B);

        }
        return DataTables::of($tasksData)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }


    public function requestedToUsFinishedTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);

        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {

            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', ['4', '7'])
                ->where('created_by', $user_id)
                ->get();
        } elseif ($type == 'requested_by_me') {
            $tasks = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', ['4', '7'])
                ->where('created_by', $user)
                ->get();
        } elseif ($type == 'total_task') {
            $tasks_A = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', ['4', '7'])
                ->where('created_by', $user_id)
                ->get();

            $tasks_B = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', ['4', '7'])
                ->where('created_by', $user)
                ->get();

            $tasks = $tasks_A->merge($tasks_B);


        }
        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function requestedToUsTotalTasks($user_id, $status_id, $type)
    {
        $user_id = decrypt($user_id);

        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {
            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                ->where('created_by', $user_id)
                ->get();
        } elseif ($type == 'requested_by_me') {
            $tasks = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                ->where('created_by', $user)
                ->get();
        } elseif ($type == 'total_task') {
            $tasks_A = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                ->where('created_by', $user_id)
                ->get();

            $tasks_B = TaskAssignee::where('user_id', $user_id)
                ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                ->where('created_by', $user)
                ->get();

            $tasks = $tasks_A->merge($tasks_B);


        }
        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }




    public function requestedToUsFooterTotalTasks($user_id, $status_id, $type)
    {

        $user_ids = explode(',', $user_id);
        // $tasks = [];

        $tasks = collect();

        $user = auth()->user()->id;


        if ($status_id == '1')  // requested_by_us
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('status', 0)
                    ->where('created_by', $user_id)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 2)  // Conceptualization
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 1)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 2) //
        {


        } elseif ($status_id == 3) //Scope Defined
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 3)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 4) //In Execution
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 5)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 5) ///For Hold
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 6)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 6)  //pending
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', [1, 3, 5, 6])
                    ->where('created_by', $user_id)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 7) //For OverDue
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('created_by', $user_id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 8) //For Todays Due
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('created_by', $user_id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 9)     // For Completed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 4)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 10)     // For Closed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 7)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 11)     // For Finished Tasks
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', ['4', '7'])
                    ->where('created_by', $user_id)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 'all')      //For Grand Total
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);
                $tasksData = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                    ->where('created_by', $user_id)
                    ->get();
                $tasks = $tasks->merge($tasksData);
            }
        }

        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);


    }

    public function requestedByUsFooterTotalTasks($user_id, $status_id, $type)
    {

        $user_ids = explode(',', $user_id);
        // $tasks = [];

        $tasks = collect();

        $user = auth()->user()->id;


        if ($status_id == '1')  // requested_by_us
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('status', 0)
                    ->where('created_by', $user)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 2)  // Conceptualization
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 1)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 2) //
        {


        } elseif ($status_id == 3) //Scope Defined
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 3)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 4) //In Execution
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 5)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 5) ///For Hold
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 6)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 6)  //pending
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', [1, 3, 5, 6])
                    ->where('created_by', $user)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 7) //For OverDue
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('created_by', $user)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 8) //For Todays Due
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('created_by', $user)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 9)     // For Completed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 4)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 10)     // For Closed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 7)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 11)     // For Finished Tasks
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', ['4', '7'])
                    ->where('created_by', $user)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 'all')      //For Grand Total
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksData = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                    ->where('created_by', $user)
                    ->get();

                $tasks = $tasks->merge($tasksData);
            }
        }

        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })
            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })
            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })
            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })
            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })
            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);


    }

    public function totalTaskFooterTotalTasks($user_id, $status_id, $type)
    {
        $user_ids = explode(',', $user_id);
        // $tasks = [];

        $tasks = collect();

        $user = auth()->user()->id;


        if ($status_id == '1')  // requested_by_us
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasks_A = TaskAssignee::where('user_id', $user)
                    ->where('status', '0')
                    ->where('created_by', $user_id)
                    ->get();

                $tasks_B = TaskAssignee::where('user_id', $user_id)
                    ->where('status', '0')
                    ->where('created_by', $user)
                    ->get();

                // Combine the results into one collection
                $tasksData = $tasks_A->merge($tasks_B);
                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 2)  // Conceptualization
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                // $tasksData = TaskAssignee::where('user_id', $user_id)
                //     ->where('task_status', 1)
                //     ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                //     ->get();

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 1)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 1)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 2) //
        {


        } elseif ($status_id == 3) //Scope Defined
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                // $tasksData = TaskAssignee::where('user_id', $user_id)
                //     ->where('task_status', 3)
                //     ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                //     ->get();

                $tasksDataA = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 3)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 3)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);


                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 4) //In Execution
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 5)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 5)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 5) ///For Hold
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 6)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 6)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 6)  //pending
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);


                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', [1, 3, 5, 6])
                    ->where('created_by', $user_id)
                    ->get();
                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', [1, 3, 5, 6])
                    ->where('created_by', $user)
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }

        } elseif ($status_id == 7) //For OverDue
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('created_by', $user_id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('created_by', $user)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '<', $cdate)
                    ->get();

                $tasksData = $tasksDataA->merge($tasksDataB);
                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 8) //For Todays Due
        {

            $cdate = date("Y-m-d");

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);


                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('created_by', $user_id)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->get();


                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('created_by', $user)
                    ->whereNotIn('task_status', [4, 7])
                    ->whereDate('due_date', '=', $cdate)
                    ->get();

                $tasksData = $tasksDataA->merge($tasksDataB);
                $tasks = $tasks->merge($tasksData);
            }


        } elseif ($status_id == 9)     // For Completed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 4)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();


                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 4)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();


                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 10)     // For Closed
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->where('task_status', 7)
                    ->where('created_by', $user_id) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->where('task_status', 7)
                    ->where('created_by', $user) // Assuming the tasks are created by the logged-in user
                    ->get();

                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 11)     // For Finished Tasks
        {

            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', ['4', '7'])
                    ->where('created_by', $user_id)
                    ->get();


                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', ['4', '7'])
                    ->where('created_by', $user)
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }
        } elseif ($status_id == 'all')      //For Grand Total
        {
            foreach ($user_ids as $user_id) {
                $user_id = decrypt($user_id);

                $tasksDataA = TaskAssignee::where('user_id', $user)
                    ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                    ->where('created_by', $user_id)
                    ->get();

                $tasksDataB = TaskAssignee::where('user_id', $user_id)
                    ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                    ->where('created_by', $user)
                    ->get();
                $tasksData = $tasksDataA->merge($tasksDataB);

                $tasks = $tasks->merge($tasksData);
            }

        }


        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })




            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);


    }

    // 27-06
    // public function accept_task($encrypted_id)
    // {
    //     try {
    //         $id = decrypt($encrypted_id);

    //         $userId = auth()->user()->id;
    //         // dd($id);
    //         $task_ass = TaskAssignee::where('user_id', $userId)
    //             ->where('task_id', $id)->first();
    //         // dd($task_ass);
    //         Taskassignee::find($task_ass->id)->update(['status' => 1]);
    //         $task = Task::where('id', $id)->first();

    //         $task->accepted_date = now()->format('Y-m-d H:i:s');
    //         $task->save();

    //         return redirect()->route("app-task-requested")->with('success', 'Task Accepted Successfully');
    //     } catch (\Exception $error) {
    //         // dd($error->getMessage());
    //         return redirect()->route("app-task-requested")->with('error', 'Error while Accepting Task');
    //     }
    // }
// 27-06
    public function accept_task($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $userId = auth()->user()->id;
            // 27-06
            // $task_ass = TaskAssignee::where('user_id', $userId)
            //     ->where('task_id', $id)->first();
            // Taskassignee::find($task_ass->id)->update(['status' => 1]);
            $task = Task::where('id', $id)->first();
            // 27-06
            $task_ass = TaskAssignee::where('user_id', $userId)
                ->where('task_id', $id)->get();
            $task_ass = TaskAssignee::where('user_id', $userId)
                ->where('task_id', $id)
                ->get();
            foreach ($task_ass as $task_assignee) {
                $task_assignee->update([
                    'status' => 1,
                    'accepted_by' => $userId, // Replace with the appropriate value or variable
                    'accepted_date' => now()
                ]);
            }
            $task->accepted_date = now()->format('Y-m-d H:i:s');
            $task->save();

            return redirect()->back()->with('success', 'Task Accepted Successfully');
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->back()->with('error', 'Error while Accepting Task');
        }
    }

    public function create()
    {
        $page_data['page_title'] = "Task";
        $page_data['form_title'] = "Add New Task";
        $task = '';
        $SubTaskData = [];
        $projects = Project::where('status', 'on')->get();
        $departments = Department::where('status', 'on')->get();
        $Subdepartments = SubDepartment::where('status', 'on')->get();
        // $Status = Status::where('status', 'on')->get();
        $Status = Status::where('status', 'on')->whereNot('id', '4')->get();
        $Prioritys = Priority::where('status', 'on')->get();
        $users = User::with('department')
            ->where('status', '1')

            ->where('id', '!=', 1)
            ->get();

        // $users = User::with('department')->where('status', '1')->where('id'!= 1) ->get();
        $departmentslist = $this->taskService->getAlltask();
        $data['department'] = Task::all();

        return view('.content.apps.task.create-edit', compact('page_data', 'SubTaskData', 'task', 'departmentslist', 'data', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys'));
    }
    // Pradips Code
    // public function store(CreateTaskRequest $request)
    // {
    //     try {
    //         $project = Project::where('id', $request->get('project_id'))->first();
    //         $priority = Priority::where('id', $request->get('priority_id'))->first();
    //         $status = Status::where('id', $request->get('task_status'))->first();


    //         if ($request->get('task_status') == 4) {
    //             $taskData['completed_date'] = now();
    //         }
    //         if ($request->get('task_status') == 7) {
    //             $taskData['close_date'] = now();
    //         }
    //         $taskData['created_by'] = auth()->user()->id;
    //         $authenticatedUserId = intval(auth()->user()->id);
    //         $userIds = $request->input('user_id', []);
    //         $userIds = array_map('intval', $userIds);
    //         $users = User::whereIn('id', $userIds)->get()->groupBy('department_id');

    //         foreach ($users as $departmentId => $departmentUsers) {
    //             $deparment_data = Department::where('id', $departmentId)->first();
    //             $taskData = [

    //                 'department_id' => $departmentId,
    //                 'department_name' => $deparment_data->department_name,
    //                 'sub_department_id' => $departmentId,
    //                 'ticket' => $request->get('task_type') == '1' ? 1 : 0,
    //                 'title' => $request->get('title'),
    //                 'description' => $request->get('description'),
    //                 'subject' => $request->get('subject'),
    //                 'project_id' => $request->get('project_id'),
    //                 'project_name' => $project->project_name,
    //                 'start_date' => $request->get('start_date'),
    //                 'due_date' => $request->get('due_date'),
    //                 'priority_id' => $request->get('priority_id'),
    //                 'priority_name' => $priority->priority_name,
    //                 'task_status' => $request->get('task_status'),
    //                 'status_name' => $status->status_name,
    //             ];
    //             if ($request->get('task_status') == 4) {
    //                 $taskData['completed_date'] = now();
    //             }
    //             if ($request->get('task_status') == 7) {
    //                 $taskData['close_date'] = now();
    //             }
    //             $taskData['created_by'] = auth()->user()->id;

    //             if (in_array($authenticatedUserId, $departmentUsers->pluck('id')->toArray())) {
    //                 $taskData['accepted_date'] = now();
    //             }

    //             $task = $this->taskService->create($taskData);
    //             $task->TaskNumber = $task->id;
    //             $task->save();

    //             if ($request->hasFile('attachments')) {
    //                 foreach ($request->file('attachments') as $attachment) {
    //                     $filenameWithExtension = $attachment->getClientOriginalName();
    //                     $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
    //                     $extension = $attachment->getClientOriginalExtension();
    //                     $storedFilename = $filename . '_' . time() . '.' . $extension;

    //                     $path = $attachment->storeAs('attachments', $storedFilename);

    //                     TaskAttachment::create([
    //                         'task_id' => $task->id,
    //                         'file' => $path,
    //                     ]);
    //                 }
    //             }

    //             $departmentUserIds = $departmentUsers->pluck('id')->toArray();

    //             $task->users()->sync($departmentUserIds);
    //             $task->users()->updateExistingPivot($departmentUserIds, ['status' => 0]);

    //             if (in_array($authenticatedUserId, $departmentUserIds)) {
    //                 $task->users()->updateExistingPivot($authenticatedUserId, ['status' => 1]);
    //             }
    //         }


    //         $authUserId = auth()->user()->id;
    //         if (in_array($authUserId, $userIds)) {
    //             $task->users()->updateExistingPivot($authUserId, ['status' => 1]);
    //         }


    //         $loggedInUser = auth()->user();
    //         $encryptedId = encrypt($task->id);
    //         $task->encryptedId = $encryptedId;

    //         $html = View::make('emails.task_created', compact('task'))->render();
    //         $subject = "New Task Created";

    //         foreach ($task->users as $user) {
    //             $taskViewUrl = route('app-task-view', ['encrypted_id' => encrypt($task->id)]); // Encrypt the task ID

    //             createNotification(
    //                 $user->id,
    //                 $task->id,
    //                 'New task ' . $task->id . ' assigned to you.<br> <a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>',
    //                 'Created'
    //             );
    //         }



    //         // die;
    //         // $mail = Mail::to('pradip12345.pv@gmail.com')->send(new TaskCreatedMail($subject, $html));
    //         if (!empty($task)) {
    //             return redirect()->route("app-task-list")->with('success', 'Task Added Successfully');
    //         } else {

    //             return redirect()->back()->with('error', ' Error while Email');
    //         }
    //     } catch (\Exception $error) {
    //         \Log::error('Error adding task: ' . $error->getMessage(), [
    //             'exception' => $error,
    //             // You can add more context if needed
    //         ]);
    //         return redirect()->route("app-task-list")->with('success', 'Task Added Successfully');
    //     }
    // }



    // public function store(CreateTaskRequest $request)
    // {
    //     try {
    //         // Handle users in the new department
    //         $authenticatedUserId = intval(auth()->user()->id);
    //         $userIds = $request->input('user_id', []);
    //         $userIds = array_map('intval', $userIds);

    //         // Fetch users and group them by their department
    //         $users = User::whereIn('id', $userIds)->get()->groupBy('department_id');

    //         // We'll fetch the department information dynamically from the users
    //         $firstDepartment = $users->keys()->first(); // Get the first department ID
    //         $department = Department::find($firstDepartment); // Get department details

    //         // Get other necessary data like project, priority, and status
    //         $project = Project::where('id', $request->get('project_id'))->first();
    //         $priority = Priority::where('id', $request->get('priority_id'))->first();
    //         $status = Status::where('id', $request->get('task_status'))->first();

    //         $taskData = [
    //             'department_id' => $firstDepartment,  // Set the department dynamically from the user data
    //             'department_name' => $department->department_name, // Department name from the first user
    //             'sub_department_id' => $firstDepartment,  // Assuming sub-department is the same as department for now
    //             'ticket' => $request->get('task_type') == '1' ? 1 : 0,
    //             'title' => $request->get('title'),
    //             'description' => $request->get('description'),
    //             'subject' => $request->get('subject'),
    //             'project_id' => $request->get('project_id'),
    //             'project_name' => $project->project_name,
    //             'start_date' => $request->get('start_date'),
    //             'due_date' => $request->get('due_date'),
    //             'priority_id' => $request->get('priority_id'),
    //             'priority_name' => $priority->priority_name,
    //             'task_status' => $request->get('task_status'),
    //             'status_name' => $status->status_name,
    //             'created_by' => auth()->user()->id,
    //         ];

    //         if ($request->get('task_status') == 4) {
    //             $taskData['completed_date'] = now();
    //         }
    //         if ($request->get('task_status') == 7) {
    //             $taskData['close_date'] = now();
    //         }

    //         // Check if the task already exists
    //         $existingTask = Task::where('project_id', $request->get('project_id'))
    //             ->where('title', $request->get('title'))
    //             ->first();

    //         if ($existingTask) {
    //             // If the task exists, we will use it
    //             $task = $existingTask;
    //         } else {
    //             // If the task does not exist, create a new one
    //             $task = $this->taskService->create($taskData);
    //             $task->TaskNumber = $task->id;
    //             $task->save();
    //         }

    //         // Handle sub-tasks for each department user
    //         $subTaskCounter = 'A'; // To generate names like "Test - A", "Test - B"
    //         foreach ($users as $departmentId => $departmentUsers) {
    //             foreach ($departmentUsers as $departmentUser) {
    //                 // Ensure unique sub-task names for each user
    //                 $subTaskData = [
    //                     'task_id' => $task->id,  // Link to the main task
    //                     'assign_to_id' => $departmentUser->id,
    //                     'name' => $task->title . ' - ' . $subTaskCounter,  // e.g., "Test - A"
    //                     'ticket' => $request->get('task_type') == '1' ? 1 : 0,
    //                     'title' => $request->get('title'),
    //                     'description' => $request->get('description'),
    //                     'department_id' => $firstDepartment,  // Set the department dynamically from the user data
    //                     'department_name' => $department->department_name, // Department name from the first user
    //                     'sub_department_id' => $firstDepartment,  // Assuming sub-department is the same as department for now
    //                     'subject' => $request->get('subject'),
    //                     'project_id' => $request->get('project_id'),
    //                     'project_name' => $project->project_name,
    //                     'start_date' => $request->get('start_date'),
    //                     'due_date' => $request->get('due_date'),
    //                     'priority_id' => $request->get('priority_id'),
    //                     'priority_name' => $priority->priority_name,
    //                     'task_status' => $request->get('task_status'),
    //                     'status_name' => $status->status_name,
    //                     'created_by' => auth()->user()->id,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];

    //                 // Create the sub-task
    //                 SubTask::create($subTaskData);

    //                 // Increment the sub-task name counter (A, B, C, etc.)
    //                 $subTaskCounter = chr(ord($subTaskCounter) + 1); // A -> B -> C
    //             }

    //             // Sync users with the main task (if needed)
    //             $departmentUserIds = $departmentUsers->pluck('id')->toArray();
    //             $task->users()->sync($departmentUserIds);
    //             $task->users()->updateExistingPivot($departmentUserIds, ['status' => 0]);

    //             // Update accepted status for the authenticated user
    //             if (in_array($authenticatedUserId, $departmentUserIds)) {
    //                 $task->users()->updateExistingPivot($authenticatedUserId, ['status' => 1]);
    //             }
    //         }

    //         // After creating the sub-tasks, send notifications as usual
    //         $authUserId = auth()->user()->id;
    //         if (in_array($authUserId, $userIds)) {
    //             $task->users()->updateExistingPivot($authUserId, ['status' => 1]);
    //         }

    //         // Email notification
    //         $loggedInUser = auth()->user();
    //         $encryptedId = encrypt($task->id);
    //         $task->encryptedId = $encryptedId;

    //         $html = View::make('emails.task_created', compact('task'))->render();
    //         $subject = "New Task Created";

    //         foreach ($task->users as $user) {
    //             $taskViewUrl = route('app-task-view', ['encrypted_id' => encrypt($task->id)]); // Encrypt the task ID

    //             createNotification(
    //                 $user->id,
    //                 $task->id,
    //                 'New task ' . $task->id . ' assigned to you.<br> <a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>',
    //                 'Created'
    //             );
    //         }

    //         return redirect()->route("app-task-list")->with('success', 'Task Added Successfully');
    //     } catch (\Exception $error) {
    //         \Log::error('Error adding task: ' . $error->getMessage(), [
    //             'exception' => $error,
    //         ]);
    //         return redirect()->route("app-task-list")->with('error', 'Error while adding task');
    //     }
    // }

    // Pradips Code

    public function store(CreateTaskRequest $request)
    {
        try {
            // dd($request->all());
            // Fetch project, priority, and status
            $project = Project::where('id', $request->get('project_id'))->first();
            $priority = Priority::where('id', $request->get('priority_id'))->first();
            $status = Status::where('id', $request->get('task_status'))->first();
            if ($request->recurring == 1) {
                $request->validate([
                    'recurring_type' => 'required|string',
                ]);

                $recurringType = $request->input('recurring_type');
                $numberOfDays = $request->input('number_of_time');
                $userIds = $request->input('user_id');
                $startDate = Carbon::parse($request->input('start_date'));

                // Loop to create tasks based on recurring type and number of days
                $tasks = [];
                $taskSave = null; // To hold the first task ID for subsequent sub-tasks

                for ($i = 0; $i < $numberOfDays; $i++) {

                    $taskStartDate = clone $startDate;
                    $taskDueDate = clone $startDate;

                    switch ($recurringType) {
                        case 'daily':
                            $taskStartDate->addDays($i);
                            $taskDueDate->addDays($i + 1); // Assuming tasks last for a day
                            break;
                        case 'weekly':
                            $taskStartDate->addWeeks($i);
                            $taskDueDate->addWeeks($i + 1); // One week later
                            break;
                        case 'monthly':
                            $taskStartDate->addMonths($i);
                            $taskDueDate->addMonths($i + 1);
                            break;
                        case 'quarterly':
                            $taskStartDate->addMonths($i * 3);
                            $taskDueDate->addMonths($i * 3 + 3); // Three months later
                            break;
                        case 'half_quarterly':
                            $taskStartDate->addMonths($i * 6);
                            $taskDueDate->addMonths($i * 6 + 6); // Six months later
                            break;
                        case 'yearly':
                            $taskStartDate->addYears($i);
                            $taskDueDate->addYears($i + 1);
                            break;
                        default:
                            break;
                    }

                    $assignedUsers = implode(',', $userIds);
                    // Prepare task data
                    $taskData = [
                        'priority_id' => $request->input('priority_id'),
                        'project_id' => $request->input('project_id'),
                        'department_id' => $request->input('department_id'),
                        'task_assignes' => $assignedUsers, // Store assigned users
                        'sub_department_id' => $request->input('sub_department_id'),
                        'task_status' => $request->input('task_status'),
                        'title' => $request->input('title'),
                        'subject' => $request->input('subject'),
                        'description' => $request->input('description'),
                        'start_date' => $taskStartDate,
                        'ticket' => $request->get('task_type') == '1' ? 1 : 0,
                        'due_date' => $taskDueDate,
                        'recurring_type' => $recurringType,
                        'number_of_days' => $numberOfDays,
                        'created_by' => auth()->id(),
                    ];

                    // If this is the first task, set 'is_sub_task' to null
                    if ($i == 0) {
                        $taskData['is_sub_task'] = null;
                        $taskSave = RecurringTask::create($taskData); // Insert the first task

                        // Store the task number after task creation
                        $taskSave->TaskNumber = $taskSave->id;
                        $taskSave->save(); // Save the task with TaskNumber

                        // Handle attachments for the main task only (first task)
                        if ($request->hasFile('attachments')) {
                            foreach ($request->file('attachments') as $attachment) {
                                // Get the original file name and its extension
                                $filenameWithExtension = $attachment->getClientOriginalName();
                                $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                                $extension = $attachment->getClientOriginalExtension();

                                // Create a unique file name by appending the current timestamp
                                $storedFilename = $filename . '_' . time() . '.' . $extension;

                                // Store the file in the 'attachments' directory
                                $path = $attachment->storeAs('attachments', $storedFilename);

                                // Now associate the attachment with the main task (first task)
                                RecursiveTaskAttachment::create([
                                    'task_id' => $taskSave->id,  // Use the first task ID for attachments
                                    'file' => $path,
                                ]);
                            }
                        }
                    } else {
                        // For subsequent tasks, associate them with the first task as a sub-task
                        $taskData['is_sub_task'] = $taskSave->id;
                        $task = RecurringTask::create($taskData); // Insert the subsequent tasks

                        // Store the task number after task creation
                        $task->TaskNumber = $task->id;
                        $task->save(); // Save the task with TaskNumber
                    }
                }

                return redirect()->route('app-task-list')->with('success', 'Recurring tasks created successfully.');
            } else {

                // Prepare task data
                $taskData = [
                    'ticket' => $request->get('task_type') == '1' ? 1 : 0,
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'subject' => $request->get('subject'),
                    'project_id' => $request->get('project_id'),
                    'project_name' => $project->project_name,
                    'start_date' => $request->get('start_date'),
                    'due_date' => $request->get('due_date_form'),
                    'priority_id' => $request->get('priority_id'),
                    'priority_name' => $priority->priority_name,
                    'task_status' => $request->get('task_status'),
                    'status_name' => $status->status_name,
                    'created_by' => auth()->user()->id,
                ];

                // Handle status-specific fields (completed_date and close_date)
                if ($request->get('task_status') == 4) {
                    $taskData['completed_date'] = now();
                }
                if ($request->get('task_status') == 7) {
                    $taskData['close_date'] = now();
                    $taskData['completed_date'] = now();

                }

                // Create the task
                $task = $this->taskService->create($taskData);
                $task->TaskNumber = $task->id;  // Set task number
                $taskCount = Task::where('id', $task->id)->count(); // Count how many tasks with the same task_id exist
                // dd($taskCount);

                $task->save();

                // Attach files if any
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $attachment) {
                        $filenameWithExtension = $attachment->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $attachment->getClientOriginalExtension();
                        $storedFilename = $filename . '_' . time() . '.' . $extension;

                        $path = $attachment->storeAs('attachments', $storedFilename);

                        TaskAttachment::create([
                            'task_id' => $task->id,
                            'file' => $path,
                        ]);
                    }
                }

                // Get the list of users and assign them to the task
                $userIds = $request->input('user_id', []);
                $userIds = array_map('intval', $userIds); // Ensure all user IDs are integers

                // Sync users to the task (assign task to all users)
                $task->users()->sync($userIds); // Sync the users

                // Update their pivot table status (0 for others, 1 for the creator)
                $taskCount = count($task->users); // Get the current number of users associated with the task
                $startingTaskNumber = 1; // Start from task number 01

                foreach ($userIds as $index => $userId) {
                    // Dynamically generate a task number for each user, starting from 01
                    $taskNumber = $task->id . '-' . str_pad($startingTaskNumber + $index, 2, '0', STR_PAD_LEFT); // Increment task number per user
                    $user = User::find($userId);  // Assuming you have a User model
                    $departmentId = $user->department_id;
                    $subdepartment = $user->subdepartment;
                    $status = (auth()->user()->id == $userId) ? 1 : 0; // If they are the same, set status to 1, otherwise 0
                    // Update pivot with user-specific task number
                    $task->users()->updateExistingPivot($userId, [
                        'status' => $status,
                        'task_status' => $request->get('task_status'),
                        'task_number' => $taskNumber,
                        'due_date' => $request->get('due_date_form'),
                        'department' => $departmentId,  // Save department_id
                        'sub_department' => $subdepartment, // Save subdepartment
                        'created_by' => auth()->user()->id,
                        'created_at' => now(),  // Use the current timestamp for created_at
                    ]);

                }
                $task = Task::where('id', $task->id)->first();
                $task->last_task_number = $taskNumber;

                // Save the updated task
                $task->save();



                $authenticatedUserId = auth()->user()->id;
                if (in_array($authenticatedUserId, $userIds)) {
                    $task->users()->updateExistingPivot($authenticatedUserId, ['status' => 1]);
                }

                // Send notifications to users
                $loggedInUser = auth()->user();
                $encryptedId = encrypt($task->id);
                $task->encryptedId = $encryptedId;

                $html = View::make('emails.task_created', compact('task'))->render();
                $subject = "New Task Created";

                foreach ($task->users as $user) {
                    $taskViewUrl = route('app-task-view', ['encrypted_id' => encrypt($task->id)]); // Encrypt the task ID

                    createNotification(
                        $user->id,
                        $task->id,
                        'New task ' . $task->id . ' assigned to you.<br> <a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>',
                        'Created'
                    );
                }

                // Redirect with success message
                return redirect()->route("app-task-list")->with('success', 'Task Added Successfully');
            }
        } catch (\Exception $error) {
            // Log any error
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while adding task');
        }
    }
    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            // dd($id);
            $task = $this->taskService->gettask($id);
            $Maintask = $this->taskService->gettask($id);
            if ($task && $task->creator->id == auth()->user()->id) {
                // dd($task);
                $creator = 1;

                $getTaskComments = Comments::where('task_id', $task->id)->get();
                // $getTaskComments = Task::where('id', $task->task_id)->first();
            } else {
                $task = $this->taskService->gettaskAssigne($id)->first();
                $getTaskComments = Comments::where('task_id', $task->task_id)->get();
                // dd($getTaskComments);
                $creator = 0;
                // dd($task);
            }

            // dd($task);


            // $SubTaskData = TaskAssignee::where('task_id', $task->id)
            //     ->where(function ($query) {
            //         $query->where('created_by', Auth::user()->id)
            //             ->orWhere('user_id', Auth::user()->id); // Check for either created_by or user_id
            //     })
            //     ->get()
            //     ->unique('task_number')  // Remove duplicate subtasks based on task_number
            //     ->sortBy(function ($subtask) {
            //         // Remove hyphen and cast the task number to integer for correct sorting
            //         return (int) str_replace('-', '', $subtask->task_number);
            //     });
            $SubTaskData = TaskAssignee::where('task_id', $task->id)
                ->where(function ($query) {
                    $query->where('created_by', Auth::user()->id)
                        ->orWhere('user_id', Auth::user()->id); // Check for either created_by or user_id
                })
                ->where('user_id', '!=', Auth::user()->id)  // Exclude tasks where user_id is the logged-in user
                ->get()
                ->unique('task_number')  // Remove duplicate subtasks based on task_number
                ->sortBy(function ($subtask) {
                    // Remove hyphen and cast the task number to integer for correct sorting
                    return (int) str_replace('-', '', $subtask->task_number);
                });


            // dd($SubTaskData);


            $page_data['page_title'] = "Task";
            $page_data['form_title'] = "Edit Task";

            $projects = Project::where('status', 'on')->get();
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $Status = Status::where('status', 'on')->get();
            $Prioritys = Priority::where('status', 'on')->get();
            //            dd($task->department_id);
            $users = User::where('status', '1')
                ->where('id', '!=', 1)
                ->get();


            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;
            // dd($creator);
            if ($creator == 1) {
                return view('.content.apps.task.create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'Maintask', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'SubTaskData', 'getTaskComments'));
            } else {
                return view('.content.apps.task.assigne-create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'Maintask', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'SubTaskData', 'getTaskComments'));

            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }
    }

    public function saveFeedback(Request $request)
    {
        // $request->validate([
        //     'subtask_id' => 'required|exists:task_assignees,id',
        //     'feedback' => 'nullable|string',
        //     'rating' => 'nullable|numeric|min:0|max:100', // Adjust the range as per your requirements
        // ]);
        // Retrieve the TaskAssignee record by subtask_id
        $subTaskId = $request->subtask_id;
        $taskAssignee = TaskAssignee::find($subTaskId);

        // Check if the record exists
        if ($taskAssignee) {
            // Update feedback and rating
            $taskAssignee->feedback = $request->feedback;
            $taskAssignee->rating = $request->rating;
            $taskAssignee->save();

            return response()->json([
                'success' => 'Feedback and rating updated successfully.',
            ], 200);
        }

        return response()->json([
            'error' => 'TaskAssignee not found.',
        ], 404);

    }
    public function recurringedit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            // dd($id);


            $task = RecurringTask::where('id', $id)->first();
            $attachmentsrec = RecursiveTaskAttachment::where('task_id', $task->id)->get();
            // dd($attachmentsrec);
            $creator = 1;

            $assignedUserIds = explode(',', $task->task_assignes);


            $page_data['page_title'] = "Task";
            $page_data['form_title'] = "Edit Task";

            $projects = Project::where('status', 'on')->get();
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $Status = Status::where('status', 'on')->get();
            $Prioritys = Priority::where('status', 'on')->get();
            //            dd($task->department_id);
            $users = User::where('status', '1')
                ->where('id', '!=', 1)
                ->get();


            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;
            // dd($creator);

            return view('.content.apps.task.recurring-create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'assignedUserIds', 'attachmentsrec'));

        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }
    }
    // public function update(UpdateTaskRequest $request, $encrypted_id)
    // {
    //     try {
    //         $id = decrypt($encrypted_id);

    //         $taskData['title'] = $request->get('title');
    //         $taskData['description'] = $request->get('description');
    //         $taskData['subject'] = $request->get('subject');
    //         $taskData['updated_by'] = auth()->user()->id;

    //         $updated = $this->taskService->updatetask($id, $taskData);
    //         if (!empty($updated)) {
    //             return redirect()->route("app-task-list")->with('success', 'Task Updated Successfully');
    //         } else {
    //             return redirect()->back()->with('error', 'Error while Updating Task');
    //         }
    //     } catch (\Exception $error) {
    //         return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
    //     }
    // }
    public function retrive($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            // Retrieve the soft-deleted task
            $task = Task::withTrashed()->findOrFail($id);

            // Check if the task is soft-deleted
            if ($task->trashed()) {
                // Restore the task
                $task->restore();
                return redirect()->route("app-task-list")->with('success', 'Task Retrieved Successfully');
            } else {
                // Task is not soft-deleted
                return redirect()->route("app-task-list")->with('error', 'Task not found or already retrieved.');
            }
        } catch (ModelNotFoundException $error) {
            // Task not found
            return redirect()->route("app-task-list")->with('error', 'Task not found.');
        } catch (\Exception $error) {
            // Other exceptions
            return redirect()->route("app-task-list")->with('error', 'Error while Retrieving Task');
        }
    }
    // public function update(UpdateTaskRequest $request, $encrypted_id)
    // {
    //     // dd($request->all());
    //     try {
    //         $id = decrypt($encrypted_id);
    //          $taskData['ticket'] = $request->get('task_type') == '1' ? 1 : 0;

    //         $taskData['title'] = $request->get('title');
    //         $taskData['description'] = $request->get('description');
    //         $taskData['subject'] = $request->get('subject');
    //         $taskData['closed'] = $request->input('closed') ? true : false;

    //         $taskData['project_id'] = $request->get('project_id');
    //         $taskData['start_date'] = $request->get('start_date');
    //         $taskData['due_date'] = $request->get('due_date');
    //         $taskData['priority_id'] = $request->get('priority_id');
    //             $taskData['task_status'] = $request->get('task_status');
    //         if ($request->get('task_status') == 4) {
    //             $taskData['completed_date'] = now();
    //         }
    //         if ($request->get('task_status') == 7) {
    //             $taskData['close_date'] = now();
    //         }
    //         $taskData['updated_by'] = auth()->user()->id;
    //         if ($request->comment != '') {
    //             $comment = new Comments();
    //             $comment->comment = $request->input('comment');
    //             $comment->task_id = $request->input('task_id');
    //             $comment->created_by = Auth::id();
    //             $comment->save();
    //         }
    //         $task = Task::where('id', $id)->first();
    //         if ($request->get('closed') == 'on' && $task->created_by == auth()->user()->id) {
    //             $taskData['task_status'] = 7;
    //         }
    //         $updated = $this->taskService->updatetask($id, $taskData);


    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $attachment) {
    //                 // Get the original filename with extension
    //                 $filenameWithExtension = $attachment->getClientOriginalName();

    //                 // Generate a unique filename to avoid collisions
    //                 $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
    //                 $extension = $attachment->getClientOriginalExtension();
    //                 $storedFilename = $filename . '_' . time() . '.' . $extension;

    //                 // Store the file in the 'attachments' directory with the new filename
    //                 $path = $attachment->storeAs('attachments', $storedFilename);

    //                 // Save the attachment record in the database
    //                 TaskAttachment::create([
    //                     'task_id' => $id, // Use the existing task ID for attachments
    //                     'file' => $path,
    //                 ]);
    //             }
    //         }


    //         $userIds = $request->input('user_id', []);
    //         // dd($userIds);
    //         $task = Task::find($id);
    //         // Retrieve the current user IDs associated with the task
    //         $currentUsers = $task->users()->pluck('users.id')->toArray();
    //         if ($currentUsers != $userIds) {
    //             $task->users()->sync($userIds);
    //             // Log the activity for the sync action
    //             $logMessage = 'Synced users to task';
    //             $this->logActivity($logMessage, $task, 'sync', auth()->user()->id, ['old' => $currentUsers, 'new' => $userIds]);
    //         }
    //         if (!empty($updated)) {
    //             return redirect()->back()->with('success', 'Task Updated Successfully');
    //             // return redirect()->route("app-task-list")->with('success', 'Task Updated Successfully');
    //         } else {
    //             return redirect()->back()->with('error', 'Error while Updating Task');
    //         }
    //     } catch (\Exception $error) {
    //         // dd($error->getMessage());
    //         return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
    //     }
    // }

    // old befor sub_task code
    // public function update(UpdateTaskRequest $request, $encrypted_id)
    // {
    //     try {
    //         $id = decrypt($encrypted_id);
    //         $project = Project::where('id', $request->get('project_id'))->first();
    //         $priority = Priority::where('id', $request->get('priority_id'))->first();
    //         $status = Status::where('id', $request->get('task_status'))->first();
    //         $taskData['ticket'] = $request->get('task_type') == '1' ? 1 : 0;
    //         $taskData['title'] = $request->get('title');
    //         $taskData['description'] = $request->get('description');
    //         $taskData['subject'] = $request->get('subject');
    //         $taskData['closed'] = $request->input('closed') ? true : false;
    //         $taskData['project_name'] = $project->project_name;
    //         $taskData['priority_name'] = $priority->priority_name;
    //         $taskData['status_name'] = $status->status_name;




    //         $taskData['project_id'] = $request->get('project_id');
    //         $taskData['start_date'] = $request->get('start_date');
    //         $taskData['due_date'] = $request->get('due_date');
    //         $taskData['priority_id'] = $request->get('priority_id');
    //         $taskData['task_status'] = $request->get('task_status');
    //         if ($request->get('task_status') == 4) {
    //             $taskData['completed_date'] = now();
    //         } else {
    //             $taskData['completed_date'] = null;
    //         }

    //         if ($request->get('task_status') == 7) {
    //             $taskData['close_date'] = now();
    //         }

    //         $taskData['updated_by'] = auth()->user()->id;

    //         if ($request->comment != '') {
    //             $comment = new Comments();
    //             $comment->comment = $request->input('comment');
    //             $comment->task_id = $request->input('task_id');
    //             $comment->created_by = Auth::id();
    //             $comment->save();
    //         }

    //         // Fetch the task being updated
    //         $task = Task::where('id', $id)->first();

    //         // If 'closed' is checked, update the task status to 7 (closed) if created by the current user
    //         if ($request->get('closed') == 'on' && $task->created_by == auth()->user()->id) {
    //             $taskData['task_status'] = 7;
    //         }

    //         // Update the task
    //         $updated = $this->taskService->updatetask($id, $taskData);

    //         // Handle file attachments
    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $attachment) {
    //                 $filenameWithExtension = $attachment->getClientOriginalName();
    //                 $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
    //                 $extension = $attachment->getClientOriginalExtension();
    //                 $storedFilename = $filename . '_' . time() . '.' . $extension;
    //                 $path = $attachment->storeAs('attachments', $storedFilename);

    //                 TaskAttachment::create([
    //                     'task_id' => $id,
    //                     'file' => $path,
    //                 ]);
    //             }
    //         }

    //         // Sync task with selected users
    //         $userIds = $request->input('user_id', []);
    //         $task = Task::find($id);
    //         $currentUsers = $task->users()->pluck('users.id')->toArray();

    //         // Array to store users who belong to the same department
    //         $usersInSameDepartment = [];

    //         // Check if any new user does not belong to the same department as the task
    //         foreach ($userIds as $userId) {
    //             $user = User::find($userId);

    //             if ($user->department_id != $task->department_id) {

    //                 $deparment_data = Department::where('id', $task->department_id)->first();
    //                 // Create a new task for the user in a different department
    //                 $newTaskData = $taskData;
    //                 $taskData['department_name'] = $deparment_data->department_name;

    //                 $newTaskData['created_by'] = auth()->user()->id;
    //                 $newTaskData['department_id'] = $user->department_id; // Set department to user's department
    //                 $newTask = Task::create($newTaskData);

    //                 // Log new task creation due to department mismatch
    //                 $this->logActivity("New task created due to department mismatch", $newTask, 'create', auth()->user()->id);

    //                 // Assign this user to the new task
    //                 $newTask->users()->sync([$userId]);
    //             } else {
    //                 // Collect users belonging to the same department
    //                 $usersInSameDepartment[] = $userId;
    //             }
    //         }
    //         foreach ($usersInSameDepartment as $userId) {
    //             $user = User::find($userId);
    //             $taskViewUrl = route('app-task-view', encrypt($task->id));

    //             // Message for task update notification
    //             $updateMessage = 'The task "' . $task->id . '" has been updated or assigned to you.<a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>';

    //             // Send notification for task update
    //             createNotification($user->id, $task->id, $updateMessage, 'Updated');



    //         }

    //         // Sync the users who belong to the same department
    //         if ($currentUsers != $usersInSameDepartment) {
    //             $task->users()->sync($usersInSameDepartment);

    //             // Log the sync action
    //             $this->logActivity('Synced users to task', $task, 'sync', auth()->user()->id, ['old' => $currentUsers, 'new' => $usersInSameDepartment]);
    //         }

    //         if (!empty($updated)) {
    //             return redirect()->back()->with('success', 'Task Updated Successfully');
    //         } else {
    //             return redirect()->back()->with('error', 'Error while Updating Task');
    //         }
    //     } catch (\Exception $error) {
    //         return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
    //     }
    // }
    // old befor sub_task code
    public function update(UpdateTaskRequest $request, $encrypted_id)
    {

        try {
            // Decrypt the encrypted task ID
            $id = decrypt($encrypted_id);
            // dd($id);
            // Fetch project, priority, and status
            $project = Project::where('id', $request->get('project_id'))->first();
            $priority = Priority::where('id', $request->get('priority_id'))->first();
            $status = Status::where('id', $request->get('task_status'))->first();

            $AssigneUserTaskId = TaskAssignee::where('task_id', $id)->first();
            $currentStatus_Assigne = $AssigneUserTaskId->task_status;
            $task = Task::findOrFail($id);
            $currentStatus_creator = $task->task_status;
            // Prepare task data
            if ($task && $task->creator->id == auth()->user()->id) {
                // dd($request->get('due_date'));
                $taskData = [
                    'ticket' => $request->get('task_type') == '1' ? 1 : 0,
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'subject' => $request->get('subject'),
                    'project_name' => $project->project_name,
                    'priority_name' => $priority->priority_name,
                    'status_name' => $status->status_name,
                    'project_id' => $request->get('project_id'),
                    'start_date' => $request->get('start_date'),
                    'due_date' => $request->get('due_date_form'),
                    'priority_id' => $request->get('priority_id'),
                    'task_status' => $request->get('task_status'),
                    'updated_by' => auth()->user()->id,
                ];
                // dd($taskData);

                // Handle task status specific fields (completed_date and close_date)
                // if ($request->get('task_status') == 4) {
                //     // dd("Completed");
                //     $taskData['completed_date'] = now();
                //     $taskData['completed_by'] = auth()->user()->id;
                // } else {
                //     $taskData['completed_date'] = null;
                // }

                // if ($request->get('task_status') == 7) {
                //     // dd("Completed");

                //     $taskData['close_date'] = now();
                //     $taskData['close_by'] = auth()->user()->id;
                // }
                // Check if the task status has changed to 4 or 7 and update dates accordingly
                if ($request->get('task_status') == 4 && $currentStatus_creator != 4) {
                    $taskData['completed_date'] = now();  // Update completed_date only if the status is set to 4
                } elseif ($request->get('task_status') == 7 && $currentStatus_creator == 4) {
                    // $taskData['completed_date'] = now();  // Update completed_date only if the status is set to 7
                } elseif ($request->get('task_status') != 4 && $request->get('task_status') != 7) {
                    // If status is neither 4 nor 7, reset completed_date
                    $taskData['completed_date'] = null;
                }
                // dd($currentStatus_creator,'Hii');
                // Update close_date if task status is set to 7 and the status has changed
                if ($request->get('task_status') == 7 &&  $currentStatus_creator == 4) {
                    $taskData['close_date'] = now();  // Only update close_date when changing to status 7
                    $taskData['close_by'] = auth()->user()->id;
                }elseif($request->get('task_status') == 7 && ($currentStatus_creator != 7 && $currentStatus_creator != 4)) {
                    $taskData['close_date'] = now();  // Only update close_date when changing to status 7
                    $taskData['close_by'] = auth()->user()->id;
                    $taskData['completed_date'] = now();
                }
                // Fetch the task to be updated
                $task = Task::findOrFail($id);

                // If task is being closed by the creator, update task status to 7 (closed)
                if ($request->get('closed') == 'on' && $task->created_by == auth()->user()->id) {
                    $taskData['task_status'] = 7;
                }

                // Get the user IDs from the request
                $userIds = $request->input('user_id', []);
                $task = Task::find($id);
                $currentAssignees = $task->assignees->pluck('user_id')->toArray(); // Assuming 'assignees' is a relation to TaskAssignee

                $removedUserIds = array_diff($currentAssignees, $userIds);
                // dd($removedUserIds);
                // Only delete those users who are removed (not present in the new list)
                if (!empty($removedUserIds)) {
                    TaskAssignee::where('task_id', $task->id)
                        ->whereIn('user_id', $removedUserIds)
                        ->whereNull('deleted_at') // Only consider non-deleted records
                        ->get()
                        ->each(function ($assignee) {
                            $assignee->delete(); // Soft delete the removed assignee
                        });
                }
                // Update the task
                $updated = $this->taskService->updateTask($id, $taskData);
            } else {
                // dd($request->get('due_date'));
                $taskData = [
                    'due_date' => $request->get('due_date_form'),
                    'task_status' => $request->get('task_status'),
                ];
                // dd($taskData);
                // Handle task status specific fields (completed_date and close_date)
                // if ($request->get('task_status') == 4) {
                //     $taskData['completed_date'] = now();
                //     $taskData['completed_by'] = auth()->user()->id;
                // } else {
                //     $taskData['completed_date'] = null;
                // }

                // if ($request->get('task_status') == 7) {
                //     $taskData['close_date'] = now();
                //     $taskData['close_by'] = auth()->user()->id;
                // }
                if ($request->get('task_status') == 4 && $currentStatus_Assigne != 4) {
                    $taskData['completed_date'] = now();
                    $taskData['completed_by'] = auth()->user()->id; // Update completed_date only if the status is set to 4
                } elseif ($request->get('task_status') == 7 && $currentStatus_Assigne == 4) {
                    // $taskData['completed_date'] = now();  // Update completed_date only if the status is set to 7
                } elseif ($request->get('task_status') != 4 && $request->get('task_status') != 7) {
                    // If status is neither 4 nor 7, reset completed_date
                    $taskData['completed_date'] = null;
                }elseif($request->get('task_status') == 7 && ($currentStatus_creator != 7 && $currentStatus_creator != 4)) {

                    $taskData['close_date'] = now();  // Only update close_date when changing to status 7
                    $taskData['close_by'] = auth()->user()->id;
                    $taskData['completed_date'] = now();
                }

                if ($request->get('closed') == 'on' && $task->created_by == auth()->user()->id) {
                    $taskData['task_status'] = 7;
                }
                if ($request->get('task_status') == 7 && $currentStatus_Assigne != 7) {
                    $taskData['close_date'] = now();  // Only update close_date when changing to status 7
                    $taskData['close_by'] = auth()->user()->id;
                }
                // Update the task with restricted fields
                $updated = $this->taskService->updateTaskAssigne($id, $taskData);
                // return redirect()->back()->with('success', 'Task Updated Successfully');

            }
            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $filenameWithExtension = $attachment->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $attachment->getClientOriginalExtension();
                    $storedFilename = $filename . '_' . time() . '.' . $extension;

                    $path = $attachment->storeAs('attachments', $storedFilename);

                    TaskAttachment::create([
                        'task_id' => $id,
                        'file' => $path,
                    ]);
                }
            }

            // Get the user IDs from the request
            $userIds = $request->input('user_id', []);
            $task = Task::find($id);

            // Get the last task assignee record to determine the new task number
            $getLastRecord = TaskAssignee::where('task_id', $task->id)
                ->withTrashed() // Include soft-deleted records
                ->orderBy('id', 'desc') // Orders by descending order of 'id'
                ->first(); // Retrieves the first record (both deleted and non-deleted)
            // dd($getLastRecord);

            // Generate the new task number





            // Assuming $getLastRecord is the last task record fetched
            $lastTaskNumber = $getLastRecord ? $getLastRecord->task_number : 'T-01';

            // Split the last task number into its prefix and sequential part
            $taskParts = explode('-', $lastTaskNumber);
            $taskPrefix = $taskParts[0]; // This is the prefix (e.g., "9176")
            $lastSequentialNumber = isset($taskParts[1]) ? (int) $taskParts[1] : 1; // The last number (e.g., "02")
            // dd($lastSequentialNumber);
            // Loop through the userIds to assign the task number
            foreach ($userIds as $userId) {

                // Increment the sequential number for each user

                // Check if the user is already assigned to the task
                $existingAssignee = TaskAssignee::where('task_id', $task->id)
                    ->where('user_id', $userId)
                    ->whereNull('deleted_at')
                    ->first();

                if ($existingAssignee) {
                    // Update the existing record
                    $existingAssignee->task_number = in_array($userId, $currentAssignees)
                        ? $existingAssignee->task_number
                        : '';

                    $existingAssignee->task_status = $task->task_status;
                    $existingAssignee->created_by = $task->created_by;
                    $existingAssignee->save(); // Save the updated record
                } else {
                    $newSequentialNumber = str_pad($lastSequentialNumber + 1, 2, '0', STR_PAD_LEFT);
                    $newTaskNumber = $taskPrefix . '-' . $newSequentialNumber;

                    // Create a new assignee record
                    TaskAssignee::create([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'task_number' => $newTaskNumber,
                        'task_status' => $task->task_status,
                        'created_by' => $task->created_by,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $lastSequentialNumber++;

                }

                // Increment the sequential number for the next user
            }












            // $lastTaskNumber = $getLastRecord ? $getLastRecord->task_number : 'T-01';
            // $taskParts = explode('-', $lastTaskNumber);
            // $newTaskNumber = $taskParts[0] . '-' . str_pad((intval($taskParts[1]) + 1), 2, '0', STR_PAD_LEFT);

            // // Get current assignees for the task
            // $currentAssignees = TaskAssignee::where('task_id', $task->id)->pluck('user_id')->toArray();

            // foreach ($userIds as $userId) {
            //     dd($newTaskNumber);
            //     // Check if the user is already assigned to the task
            //     $existingAssignee = TaskAssignee::where('task_id', $task->id)
            //         ->where('user_id', $userId)
            //         ->whereNull('deleted_at')
            //         ->first();
            //     // dd($existingAssignee);

            //     if ($existingAssignee) {
            //         // Update the existing record
            //         $existingAssignee->task_number = in_array($userId, $currentAssignees)
            //             ? $existingAssignee->task_number
            //             : $newTaskNumber;

            //         $existingAssignee->task_status = $task->task_status;
            //         $existingAssignee->created_by = $task->created_by;
            //         $existingAssignee->save(); // Save the updated record
            //     } else {
            //         // Create a new assignee record
            //         TaskAssignee::create([
            //             'task_id' => $task->id,
            //             'user_id' => $userId,
            //             'task_number' => $newTaskNumber,
            //             'task_status' => $task->task_status,
            //             'created_by' => $task->created_by,
            //             'created_at' => Carbon::now(), // Ensure the timestamp is set
            //             'updated_at' => Carbon::now(),
            //         ]);
            //     }

            // }





            $userIds = $request->input('user_id', []);
            $task = Task::find($id);  // Re-fetch task if needed

            $lastTaskNumber = $task->last_task_number;


            // Send notification to all selected users about the update
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                $taskViewUrl = route('app-task-view', encrypt($task->id));

                // Message for task update notification
                $updateMessage = 'The task "' . $task->id . '" has been updated or assigned to you.<a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>';

                // Send notification for task update
                createNotification($user->id, $task->id, $updateMessage, 'Updated');
            }



            if ($request->comment_form != '') {
                // Create a new comment
                $comment = new Comments();
                $comment->comment = $request->get('comment_form');
                $comment->task_id = $request->get('task_id');
                $comment->created_by = auth()->id();

                // Check if 'comments_for' is empty or null
                if (empty($request->comments_for)) {
                    // If 'comments_for' is empty or null, store task creator's ID in 'to_user_id'
                    $comment->to_user_id = $request->task_created_by;
                } else {
                    // Otherwise, store the comma-separated list of user IDs in 'to_user_id'
                    $comment->to_user_id = implode(',', $request->comments_for);
                }
                // dd($comment);
                // Save the comment
                $comment->save();
            }




            // Redirect based on success or failure
            if ($updated) {
                return redirect()->back()->with('success', 'Task Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while updating task');
            }

        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }
    }


    public function recurringUpdate(UpdateTaskRequest $request, $encrypted_id)
    {
        try {
            // Decrypt task ID
            $id = decrypt($encrypted_id);

            // Retrieve the recurring task
            $task = RecurringTask::findOrFail($id);
            // dd($task);
            // New number_of_time from the request
            $newNumberOfTime = $request->get('number_of_time');

            // // Check if the new value for number_of_time is greater than the current value
            // if ($newNumberOfTime > $task->number_of_days) {
            //     // Inform the user that the new number_of_time cannot be greater than the current value
            //     return redirect()->back()->with('error', 'The new number of days cannot exceed the current number of days (' . $task->number_of_days . ')');
            // }

            // Retrieve project, priority, and status from the request
            $project = Project::where('id', $request->get('project_id'))->first();
            $priority = Priority::where('id', $request->get('priority_id'))->first();
            $status = Status::where('id', $request->get('task_status'))->first();

            // Prepare task data to update
            $taskData = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'subject' => $request->get('subject'),
                'task_status' => $request->get('task_status'),
                'number_of_days' => $newNumberOfTime,  // Updated number_of_time
                'recurring_type' => $request->get('recurring_type'),
                'updated_by' => auth()->user()->id,
            ];

            // Handle user assignees
            $userIds = $request->input('user_id', []);
            $currentAssignees = explode(',', $task->task_assignes);
            $removedUserIds = array_diff($currentAssignees, $userIds);

            if (!empty($removedUserIds)) {
                TaskAssignee::where('task_id', $task->id)
                    ->whereIn('user_id', $removedUserIds)
                    ->whereNull('deleted_at') // Only non-deleted records
                    ->get()
                    ->each(function ($assignee) {
                        $assignee->delete(); // Soft delete removed assignees
                    });
            }

            // Update the recurring task itself
            $updated = $this->taskService->updateTaskRecurring($id, $taskData);

            if ($updated) {
                // Delete all current subtasks related to the edited task
                $subtasks = RecurringTask::where('is_sub_task', $task->id)->get();
                foreach ($subtasks as $subtask) {
                    $subtask->delete(); // Delete all existing subtasks
                }

                // Create new subtasks based on the updated number_of_time
                $taskSave = null; // To hold the first task ID for subsequent sub-tasks

                // Calculate the start date based on the main task's start date
                $startDate = \Carbon\Carbon::parse($task->start_date);
                $dueDate = \Carbon\Carbon::parse($task->due_date); // Start with the main task's due_date
                $recurringType = $task->recurring_type;
                $numberOfDays = $newNumberOfTime; // Use the new number of time
                if($task->is_completed != 1)
                {
                    $numberOfDays = $newNumberOfTime - 1;
                }

                for ($i = 0; $i < $numberOfDays; $i++) {
                    $taskStartDate = clone $dueDate; // Set task's start date as the previous due date
                    $taskDueDate = clone $dueDate; // Set task's due date as the previous due date

                    // Calculate the recurring task dates based on type
                    switch ($recurringType) {
                        case 'daily':
                            $taskStartDate->addDays($i); // Start each day after the previous due date
                            $taskDueDate->addDays($i + 1); // Add 1 day for the due date
                            break;
                        case 'weekly':
                            $taskStartDate->addWeeks($i); // Add weeks to the previous due date
                            $taskDueDate->addWeeks($i + 1); // Add 1 week to the due date
                            break;
                        case 'monthly':
                            $taskStartDate->addMonths($i); // Add months to the previous due date
                            $taskDueDate->addMonths($i + 1); // Add 1 month to the due date
                            break;
                        case 'quarterly':
                            $taskStartDate->addMonths($i * 3); // Add 3 months per iteration
                            $taskDueDate->addMonths($i * 3 + 3); // Add 3 months for due date
                            break;
                        case 'half_quarterly':
                            $taskStartDate->addMonths($i * 6); // Add 6 months per iteration
                            $taskDueDate->addMonths($i * 6 + 6); // Add 6 months for due date
                            break;
                        case 'yearly':
                            $taskStartDate->addYears($i); // Add years to the previous due date
                            $taskDueDate->addYears($i + 1); // Add 1 year for the due date
                            break;
                        default:
                            break;
                    }

                    // Prepare task data for the subtask
                    $assignedUsers = implode(',', $userIds);
                    $taskData = [
                        'priority_id' => $request->input('priority_id'),
                        'project_id' => $request->input('project_id'),
                        'department_id' => $request->input('department_id'),
                        'task_assignes' => $assignedUsers, // Store assigned users
                        'sub_department_id' => $request->input('sub_department_id'),
                        'task_status' => $request->input('task_status'),
                        'title' => $request->input('title'),
                        'subject' => $request->input('subject'),
                        'description' => $request->input('description'),
                        'start_date' => $taskStartDate,
                        'ticket' => $request->get('task_type') == '1' ? 1 : 0,
                        'due_date' => $taskDueDate,
                        'recurring_type' => $recurringType,
                        'number_of_days' => $numberOfDays,
                        'created_by' => auth()->id(),
                        'is_sub_task' => $id, // Set the edited task ID as the is_sub_task
                    ];

                    // Create the subtask
                    $taskSave = RecurringTask::create($taskData); // Insert the new task

                    // Update the task number
                    $taskSave->TaskNumber = $taskSave->id;
                    $taskSave->save(); // Save the task with TaskNumber

                    // Handle attachments for the first subtask
                    if ($request->hasFile('attachments') && $i == 0) { // Only attach for the first subtask
                        foreach ($request->file('attachments') as $attachment) {
                            // Get the original file name and its extension
                            $filenameWithExtension = $attachment->getClientOriginalName();
                            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                            $extension = $attachment->getClientOriginalExtension();

                            // Create a unique file name by appending the current timestamp
                            $storedFilename = $filename . '_' . time() . '.' . $extension;

                            // Store the file in the 'attachments' directory
                            $path = $attachment->storeAs('attachments', $storedFilename);

                            // Now associate the attachment with the first subtask
                            RecursiveTaskAttachment::create([
                                'task_id' => $taskSave->id,  // Use the first task ID for attachments
                                'file' => $path,
                            ]);
                        }
                    }

                    // Update the due date for the next iteration
                    $dueDate = $taskDueDate; // Set the last created due date as the new due date for the next iteration
                }

                // Redirect with success message
                return redirect()->back()->with('success', 'Recurring Task and Subtasks Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while updating Recurring task');
            }
        } catch (\Exception $error) {
            // Log the error
            dd($error->getMessage());
            // Return error message
            return redirect()->back()->with('error', 'Error while editing Recurring Task');
        }
    }

    public function updateTaskFromView($encrypted_id, $status)
    {
        $res = ['status' => 0, 'message' => ''];
        try {
            $id = decrypt($encrypted_id);
            $taskData['task_status'] = decrypt($status);
            $taskData['updated_by'] = auth()->user()->id;

            $updated = $this->taskService->updatetask($id, $taskData);

            if (!empty($updated)) {
                $res['status'] = 1;
                $res['message'] = 'Your task has been moved.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Error while Updating Task';
            }
        } catch (\Exception $error) {
            $res['status'] = 0;
            $res['message'] = 'Error while editing Task';
        }
        return response()->json($res);
    }

    public function destroy($encrypted_id)
    {
        dd("sd");
        try {
            $id = decrypt($encrypted_id);

            $taskData['deleted_by'] = Auth()->user()->id;
            $updated = $this->taskService->updatetask($id, $taskData);
            $deleted = $this->taskService->deletetask($id);
            if (!empty($deleted)) {
                return redirect()->route("app-task-list")->with('success', 'Task Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Task');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }
    }


    public function recurringDestroy($encrypted_id)
    {
        // dd("sdeeeeeeeeeeeee");
        try {
            $id = decrypt($encrypted_id);

            $taskData['deleted_by'] = Auth()->user()->id;
            // $updated = $this->taskService->updatetask($id, $taskData);
            $deleted = $this->taskService->deleteTaskrec($id);
            if (!empty($deleted)) {
                return redirect()->route("app-task-recurring_main")->with('success', 'Task Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Task');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-recurring_main")->with('error', 'Error while editing Task');
        }
    }

    public function getSubDepartments($department_id)
    {
        $subDepartments = SubDepartment::where('department_id', $department_id)->get();

        return response()->json($subDepartments);
    }


    public function getUsersByDepartment($department_id)
    {
        $users = User::where('subdepartment', $department_id)->get();
        return response()->json($users);
    }

    public function getAll_conceptualization()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 1)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            });
        if ($userId == 1) {
            $tasks = Task::where('task_status', 1);
        }
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }
    public function getAll_close()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 7)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 7);
            });
        if ($userId == 1) {
            $tasks = Task::where('task_status', 7);
        }
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_due_date_past()
    {
        $userId = auth()->user()->id;
        // $tasks = Task::where('task_status', 2)
        //     ->whereHas('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId)
        //             ->where('status', 1);
        //     })
        //     ->get();
        // $tasks = Task::where('due_date', '<', today()) // Select tasks with due date in the past
        //     ->whereHas('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId); // Filter tasks assigned to authenticated user
        //     })
        //     ->with([
        //         'assignees' => function ($query) use ($userId) {
        //             $query->where('user_id', $userId); // Load only assignees for authenticated user
        //         }
        // ]);

        //pradip running code
        // $tasks = Task::whereNot('task_status', '7')->where('due_date', '<', today()) // Select tasks with due date in the past
        //     ->whereHas('assignees', function ($query) use ($userId) {
        //         $query->where('user_id', $userId); // Filter tasks assigned to authenticated user
        //     })
        //     ->with([
        //         'assignees' => function ($query) use ($userId) {
        //             $query->where('user_id', $userId); // Load only assignees for authenticated user
        //         }
        //     ]);
        // if ($userId == 1) {
        //     $tasks = Task::whereNot('task_status', '7')
        //         ->where('due_date', '<', today());
        // }

        //pradip running code

        // parth changes as per requrment
        if (auth()->user()->hasRole('Super Admin')) {
            $tasks = Task::where('task_status', '!=', '7')
                ->where('due_date', '<', Carbon::today())
                ->where(function ($query) {
                    $query->whereNull('completed_date') // Consider tasks not completed yet
                        ->orWhere('completed_date', '>', DB::raw('due_date')); // Or completed date is greater than due date
                });
        } else {
            $tasks = Task::where('task_status', '!=', 7)
                ->where('due_date', '<', today())
                ->where(function ($query) {
                    $query->whereNull('completed_date')
                        ->orWhere('completed_date', '>', DB::raw('due_date'));
                })
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
        }
        // parth changes as per requrment
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_scope_defined()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 3)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            });
        if ($userId == 1) {
            $tasks = Task::where('task_status', 3);
        }
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_completed()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 4)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
                // ->where('status', 1);
            });
        if ($userId == 1) {
            $tasks = Task::where('task_status', 4);
        }
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_in_execution()
    {
        $userId = auth()->user()->id;
        if (auth()->user()->hasRole('Super Admin')) {
            $tasks = Task::where('task_status', '5');
        } else {
            $tasks = Task::where('task_status', 5)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
        }

        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_hold()
    {
        $userId = auth()->user()->id;
        if (auth()->user()->hasRole('Super Admin')) {
            $tasks = Task::where('task_status', 6);
        } else {
            $tasks = Task::where('task_status', 6)
                ->whereHas('assignees', function ($query) use ($userId) {
                    $query->where('user_id', $userId);

                });

        }

        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_admin_acc()
    {
        $userId = auth()->user()->id;
        $tasks = Task::whereHas('assignees', function ($query) {
            $query->where('status', 1);
        });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

        })->addColumn('created_by_username', function ($row) {
            return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            $assignedNames = $row->users->map(function ($user) {
                return $user->first_name . ' ' . $user->last_name;
            })->implode(', ');

            return $assignedNames ?? '-';
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }
    public function getAll_deleted()
    {
        $userId = auth()->user()->id;
        if ($userId == 1) {
            $tasks = Task::onlyTrashed()->get();
        } else {
            $tasks = Task::onlyTrashed()->where('created_by', $userId)->get();
        }



        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Recover Task' class='btn-sm btn-info confirm-retrieve  me-1'data-idos='.$encryptedId' href='" . route('app-task-retrive', $encryptedId) . "'><i class='ficon' data-feather='download-cloud'></i></a>";

            $buttons = $updateButton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return '-';
            }


        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            $assignedNames = $row->users->map(function ($user) {
                return $user->first_name . ' ' . $user->last_name;
            })->implode(', ');

            return $assignedNames ?? '-';
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }
    public function getAll_admin_req()
    {
        $userId = auth()->user()->id;
        $tasks = Task::whereHas('assignees', function ($query) {
            $query->where('status', 0);
        });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

        })->addColumn('created_by_username', function ($row) {
            return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            $assignedNames = $row->users->map(function ($user) {
                return $user->first_name . ' ' . $user->last_name;
            })->implode(', ');

            return $assignedNames ?? '-';
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_admin_rej()
    {
        $userId = auth()->user()->id;
        $tasks = Task::whereHas('assignees', function ($query) {
            $query->where('status', 2);
        });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
//    dd(implode(', ' ,$row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray()));
            $assignedNames = implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray()) ?? "-";

            return $assignedNames ?? '-';
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_admin_total()
    {
        $userId = auth()->user()->id;
        $tasks = Task::whereHas('assignees', function ($query) {
            $query->where('status', 1);
        });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
            $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
            } else {
                return "-";
            }
        })->addColumn('task_Assign', function ($row) {
            // Get all names assigned to this task
            if ($row->users) {
                return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
            } else {
                return "-";
            }
        })->addColumn('task_status_name', function ($row) {
            return $row->taskStatus->status_name ?? "-";
        })
            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department->department_name ?? "-";
            })
            ->addColumn('sub_department_name', function ($row) {

                return $row->sub_department->sub_department_name ?? "-";
            })->addColumn('created_by_department', function ($row) {
                if ($row->creator && $row->creator->department) {
                    return $row->creator->department->department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_sub_department', function ($row) {
                if ($row->creator && $row->creator->sub_department) {
                    return $row->creator->sub_department->sub_department_name ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('created_by_phone_no', function ($row) {
                if ($row->creator && $row->creator->phone_no) {
                    return $row->creator->phone_no ?? '-';
                } else {
                    return "-";
                }
            })->addColumn('description', function ($row) {
                $description = html_entity_decode($row->description);
                return $description;
            })->rawColumns(['actions'])->make(true);

    }

    // public function getAll_total_task()
    // {
    //     $userId = auth()->user()->id;
    //     $user = auth()->user();
    //     if ($userId == 1) {
    //         $tasks = Task::where('task_status', '!=', 2)->get();
    //     } else {
    //         $my_task = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
    //             ->where(function ($query) use ($userId) {
    //                 $query->where('tasks.created_by', $userId)
    //                     ->orWhere('task_assignees.user_id', $userId);
    //             })
    //             ->where('task_assignees.status', 1)
    //             ->select('tasks.*'); // Selecting all columns from tasks

    //         $taccepted_by_me = Task::whereHas('assignees', function ($query) use ($user) {
    //             $query->where('user_id', $user->id)->where('status', '1');
    //         })
    //             ->whereNotIn('created_by', [$user->id])
    //             ->select('tasks.*'); // Selecting all columns from tasks

    //         $assign_by_me = Task::where('created_by', $userId)
    //             ->whereDoesntHave('assignees', function ($query) use ($userId) {
    //                 $query->where('user_id', $userId);
    //             })
    //             ->select('tasks.*'); // Selecting all columns from tasks

    //         $requested_me = Task::leftJoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
    //             ->where('task_assignees.user_id', $userId)
    //             ->where('task_assignees.status', 0)
    //             ->where('tasks.created_by', '!=', $userId)
    //             ->select('tasks.*'); // Selecting all columns from tasks

    //         $tasks = $my_task->union($taccepted_by_me)->union($assign_by_me)->union($requested_me)->get();


    //     }

    //     return DataTables::of($tasks)->addColumn('actions', function ($row) {
    //         $encryptedId = encrypt($row->id);
    //         $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' traget=_blank><i class='ficon' data-feather='edit'></i></a>";
    //         $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
    //         $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
    //         $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
    //         return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";

    //     })->addColumn('created_by_username', function ($row) {
    //         if ($row->creator) {
    //             return $row->creator->first_name . " " . $row->creator->last_name ?? '-';
    //         } else {
    //             return "-";
    //         }
    //     })->addColumn('task_Assign', function ($row) {
    //         // Get all names assigned to this task
    //         if ($row->users) {
    //             return implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray());
    //         } else {
    //             return "-";
    //         }
    //     })->addColumn('task_status_name', function ($row) {
    //         return $row->taskStatus->status_name ?? "-";
    //     })
    //         ->addColumn('project_name', function ($row) {
    //             return $row->project->project_name ?? "-";
    //         })
    //         ->addColumn('department_name', function ($row) {
    //             return $row->department->department_name ?? "-";
    //         })
    //         ->addColumn('sub_department_name', function ($row) {

    //             return $row->sub_department->sub_department_name ?? "-";
    //         })->addColumn('created_by_department', function ($row) {
    //             if ($row->creator && $row->creator->department) {
    //                 return $row->creator->department->department_name ?? '-';
    //             } else {
    //                 return "-";
    //             }
    //         })->addColumn('created_by_sub_department', function ($row) {
    //             if ($row->creator && $row->creator->sub_department) {
    //                 return $row->creator->sub_department->sub_department_name ?? '-';
    //             } else {
    //                 return "-";
    //             }
    //         })->addColumn('created_by_phone_no', function ($row) {
    //             if ($row->creator && $row->creator->phone_no) {
    //                 return $row->creator->phone_no ?? '-';
    //             } else {
    //                 return "-";
    //             }
    //         })->rawColumns(['actions'])->make(true);

    // }
// 04-06
    // public function getAll_total_task()
    // {
    //     $userId = auth()->user()->id;
    //     $user = auth()->user();
    //     $tasks = [];

    //     // Function to recursively retrieve the hierarchy
    //     function getHierarchy($userId, &$allUsers, &$addedUserIds)
    //     {
    //         // Retrieve users reporting to the given user ID
    //         $reportingUsers = User::where('report_to', $userId)->get();

    //         foreach ($reportingUsers as $user) {
    //             if (!in_array($user->id, $addedUserIds)) {
    //                 // Add the current user to the list of all users and mark its ID as added
    //                 $allUsers[$user->id] = $user;
    //                 $addedUserIds[] = $user->id;

    //                 // Recursively retrieve the hierarchy of users reporting to the current user
    //                 getHierarchy($user->id, $allUsers, $addedUserIds);
    //             }
    //         }
    //     }

    //     // Start retrieving the hierarchy from the logged-in user
    //     $allUsers = [];
    //     $addedUserIds = [$userId];
    //     getHierarchy($userId, $allUsers, $addedUserIds);

    //     // Retrieve tasks for all users in the hierarchy
    //     if ($userId == 1) {
    //         $tasks = Task::where('task_status', '!=', 2)->get();
    //     } else {
    //         $tasks = Task::whereIn('created_by', $addedUserIds)
    //             ->orWhereHas('assignees', function ($query) use ($addedUserIds) {
    //                 $query->whereIn('user_id', $addedUserIds);
    //             })
    //             ->select('tasks.*')
    //             ->get();
    //     }

    //     // Return the tasks as DataTables response
    //     return DataTables::of($tasks)
    //         ->addColumn('actions', function ($row) {
    //             $encryptedId = encrypt($row->id);
    //             // Define action buttons
    //             $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' traget=_blank><i class='ficon' data-feather='edit'></i></a>";
    //             $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
    //             $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
    //             // Concatenate buttons
    //             $buttons = $updateButton . " " . $deleteButton . " " . $viewbutton;
    //             // Return buttons wrapped in a div
    //             return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
    //         })
    //         ->addColumn('created_by_username', function ($row) {
    //             // Return creator's full name if available, otherwise '-'
    //             return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name ?? '-' : "-";
    //         })
    //         ->addColumn('task_Assign', function ($row) {
    //             // Get all names assigned to this task
    //             return $row->users ? implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray()) : "-";
    //         })
    //         ->addColumn('task_status_name', function ($row) {
    //             // Return task status name or '-'
    //             return $row->taskStatus ? $row->taskStatus->status_name : "-";
    //         })
    //         ->addColumn('project_name', function ($row) {
    //             // Return project name or '-'
    //             return $row->project ? $row->project->project_name : "-";
    //         })
    //         ->addColumn('department_name', function ($row) {
    //             // Return department name or '-'
    //             return $row->department ? $row->department->department_name : "-";
    //         })
    //         ->addColumn('sub_department_name', function ($row) {
    //             // Return sub department name or '-'
    //             return $row->sub_department ? $row->sub_department->sub_department_name : "-";
    //         })
    //         ->addColumn('created_by_department', function ($row) {
    //             // Return creator's department name or '-'
    //             return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
    //         })
    //         ->addColumn('created_by_sub_department', function ($row) {
    //             // Return creator's sub department name or '-'
    //             return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
    //         })
    //         ->addColumn('created_by_phone_no', function ($row) {
    //             // Return creator's phone number or '-'
    //             return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
    //         })
    //         ->rawColumns(['actions']) // Declare 'actions' column as raw HTML
    //         ->make(true); // Return DataTables response
    // }
    // 04-06

    //    3-sep-2024
//    public function getAll_total_task(Request $request)
//    {
//        $userId = auth()->user()->id;
//        $user = auth()->user();
//        $tasks = [];
//        ini_set('memory_limit', '256M');
//
//
//        // Function to recursively retrieve the hierarchy
//        function getHierarchy($userId, &$allUsers, &$addedUserIds)
//        {
//            $reportingUsers = User::where('report_to', $userId)->get();
//            foreach ($reportingUsers as $user) {
//                if (!in_array($user->id, $addedUserIds)) {
//                    $allUsers[$user->id] = $user;
//                    $addedUserIds[] = $user->id;
//                    getHierarchy($user->id, $allUsers, $addedUserIds);
//                }
//            }
//        }
//
//        $allUsers = [];
//        $addedUserIds = [$userId];
//        getHierarchy($userId, $allUsers, $addedUserIds);
//
//        $query = Task::query();
//
//        // Apply filters
//        // if ($request->has('title') && $request->title != '') {
//        //     // dd( $request->title);
//        //     $query->where('title', 'like', '%' . $request->title . '%');
//
//        // }
//        if ($request->has('assignees') && !empty($request->assignees)) {
//            // dd($request->assignees);
//            $query->whereHas('assignees', function ($q) use ($request) {
//                $q->whereIn('user_id', $request->assignees);
//            });
//        }
//        if ($request->has('status') && $request->status != '') {
//            $query->where('task_status', $request->status);
//        }
//        if ($request->has('task') && $request->task != '') {
//            $query->where('ticket', $request->task);
//        }
//
//        if ($request->has('dt_date') && $request->dt_date != '') {
//            $startDateParts = explode(' to ', $request->dt_date);
//
//            if (count($startDateParts) === 2) {
//                $startDate = trim($startDateParts[0]);
//                $endDate = trim($startDateParts[1]);
//
//                // Specify the format when parsing the dates
//                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                // dd($endDate);
//
//                $query->whereDate('start_date', '>=', $startDate)->whereDate('start_date', '<=', $endDate);
//            }
//        }
//        if ($request->has('end_date') && $request->end_date != '') {
//            $dueDateParts = explode(' to ', $request->end_date);
//
//            if (count($dueDateParts) === 2) {
//                $startDate = trim($dueDateParts[0]);
//                $endDate = trim($dueDateParts[1]);
//
//                // Specify the format when parsing the dates
//                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                $query->whereDate('due_date', '>=', $startDate)->whereDate('due_date', '<=', $endDate);
//            }
//        }
//        if ($request->has('accepted_task_date') && $request->accepted_task_date != '') {
//            $acceptedDateParts = explode(' to ', $request->accepted_task_date);
//
//            if (count($acceptedDateParts) === 2) {
//                $startDate = trim($acceptedDateParts[0]);
//                $endDate = trim($acceptedDateParts[1]);
//
//                // Specify the format when parsing the dates
//                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                $query->whereDate('accepted_date', '>=', $startDate)->whereDate('accepted_date', '<=', $endDate);
//            }
//        }
//
//        if ($request->has('created_by') && $request->created_by != '') {
//            $query->where('created_by', $request->created_by);
//        }
//        if ($request->has('department') && $request->department != '') {
//            $query->where('department_id', $request->department);
//        }
//        if ($request->has('start_date') && $request->start_date != '') {
//            $query->whereDate('start_date', $request->start_date);
//        }
//
//        if ($userId == 1 || auth()->user()->hasRole('Super Admin')) {
//            $query->where('task_status', '!=', 2);
//        } else {
//
//            $query = Task::query();
//
//            if ($request->has('assignees') && !empty($request->assignees)) {
//
//                $query->whereHas('assignees', function ($q) use ($request) {
//                    $q->whereIn('user_id', $request->assignees);
//                });
//            }
//            if ($request->has('status') && $request->status != '') {
//                $query->where('task_status', $request->status);
//            }
//            if ($request->has('task') && $request->task != '') {
//                $query->where('ticket', $request->task);
//            }
//
//            if ($request->has('dt_date') && $request->dt_date != '') {
//                $startDateParts = explode(' to ', $request->dt_date);
//
//                if (count($startDateParts) === 2) {
//                    $startDate = trim($startDateParts[0]);
//                    $endDate = trim($startDateParts[1]);
//
//                    // Specify the format when parsing the dates
//                    $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                    $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                    // dd($endDate);
//
//                    $query->whereDate('start_date', '>=', $startDate)->whereDate('start_date', '<=', $endDate);
//                }
//            }
//            if ($request->has('end_date') && $request->end_date != '') {
//                $dueDateParts = explode(' to ', $request->end_date);
//
//                if (count($dueDateParts) === 2) {
//                    $startDate = trim($dueDateParts[0]);
//                    $endDate = trim($dueDateParts[1]);
//
//                    // Specify the format when parsing the dates
//                    $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                    $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                    $query->whereDate('due_date', '>=', $startDate)->whereDate('due_date', '<=', $endDate);
//                }
//            }
//            if ($request->has('accepted_task_date') && $request->accepted_task_date != '') {
//                $acceptedDateParts = explode(' to ', $request->accepted_task_date);
//
//                if (count($acceptedDateParts) === 2) {
//                    $startDate = trim($acceptedDateParts[0]);
//                    $endDate = trim($acceptedDateParts[1]);
//
//                    // Specify the format when parsing the dates
//                    $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
//                    $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
//
//                    $query->whereDate('accepted_date', '>=', $startDate)->whereDate('accepted_date', '<=', $endDate);
//                }
//            }
//
//            if ($request->has('created_by') && $request->created_by != '') {
//                $query->where('created_by', $request->created_by);
//            }
//            if ($request->has('department') && $request->department != '') {
//                $query->where('department_id', $request->department);
//            }
//            if ($request->has('start_date') && $request->start_date != '') {
//                $query->whereDate('start_date', $request->start_date);
//            }
//
//
//
//            $query->where(function ($query) use ($addedUserIds) {
//                $query->whereIn('created_by', $addedUserIds)
//                    ->orWhereHas('assignees', function ($q) use ($addedUserIds) {
//                        $q->whereIn('user_id', $addedUserIds);
//                    });
//            });
//
//            // $query->where(function ($query) use ($addedUserIds) {
//            //     $query->where('task_status', '!=', 2)
//            //         ->whereIn('created_by', $addedUserIds);
//            // })->orWhereHas('assignees', function ($q) use ($addedUserIds) {
//            //     $q->whereIn('user_id', $addedUserIds);
//            // });
//        }
//
//        $tasks = $query->select('tasks.*');
//
//        return DataTables::of($tasks)
//            ->addColumn('actions', function ($row) {
//                $encryptedId = encrypt($row->id);
//                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' traget=_blank><i class='ficon' data-feather='edit'></i></a>";
//                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
//                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
//                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $deleteButton . " " . $viewbutton . "</div>";
//            })
//            ->addColumn('created_by_username', function ($row) {
//                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name ?? '-' : "-";
//            })
//            ->addColumn('task_Assign', function ($row) {
//                return $row->users ? implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray()) : "-";
//            })
//            ->addColumn('task_status_name', function ($row) {
//                return $row->taskStatus ? $row->taskStatus->status_name : "-";
//            })
//            ->addColumn('project_name', function ($row) {
//                return $row->project ? $row->project->project_name : "-";
//            })
//            ->addColumn('department_name', function ($row) {
//                return $row->department ? $row->department->department_name : "-";
//            })
//            ->addColumn('sub_department_name', function ($row) {
//                return $row->sub_department ? $row->sub_department->sub_department_name : "-";
//            })
//            ->addColumn('created_by_department', function ($row) {
//                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
//            })
//            ->addColumn('created_by_sub_department', function ($row) {
//                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
//            })
//            ->addColumn('created_by_phone_no', function ($row) {
//                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
//            })
//            ->filterColumn('task_number', function ($query, $keyword) {
//                $query->where('task_number', 'like', "%{$keyword}%");
//            })
//            ->rawColumns(['actions'])
//            ->make(true);
//    }
// 3-sep-2024





    public function getAll_total_task(Request $request)
    {
        $userId = Auth()->user()->id;
        ini_set('memory_limit', '2048M'); // Retain memory limit increase, but we'll use chunking to minimize memory usage

        // Common query for all tasks
        $query = TaskAssignee::query();

        if ($userId == 1) {
            // Admin fetches tasks by their statuses
            $query->whereIn('task_status', ['1', '3', '4', '5', '6', '7']); // Use a single query for all statuses
        } else {
            // User-specific task filters
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('created_by', $userId)
                    ->whereHas('user', function ($q) {
                        // Ensure the user is not deleted (i.e., deleted_at is null)
                        $q->whereNull('deleted_at');
                    });
            });
        }

        if ($task_filter = $request->input('task')) {
            // Assuming you want to filter by 'ticket' column in the 'tasks' table, make sure you join the tasks table
            $query->whereHas('task', function ($q) use ($task_filter) {
                $q->where('ticket', $task_filter);
            });
        }

        if ($department_filter = $request->input('department')) {
            $query->where('department', $department_filter);
        }

        if ($created_by = $request->input('created_by')) {
            $query->where('created_by', $created_by);
        }

        if ($assignees = $request->input('assignees')) {
            $query->whereHas('user', function ($q) use ($assignees) {
                $q->whereIn('user_id', $assignees);
            });
        }

        if ($status = $request->input('status')) {
            $query->where('task_status', $status);
        }

        // Date filters
        if ($request->input('dt_date')) {
            $dtDateRange = parseDateRange($request->input('dt_date'));

            $query->whereHas('task', function ($q) use ($task_filter, $dtDateRange, $request) {
                if (!empty($dtDateRange[1])) {
                    // Both start and end dates are available
                    $q->whereBetween('start_date', [$dtDateRange[0], $dtDateRange[1]]);
                } else {
                    $inputDate = $request->input('dt_date');
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                    // Only a single date is provided
                    $q->whereDate('start_date', $formattedDate);
                }
            });
        }



        if ($request->input('accepted_task_date')) {
            $dtDateRange = parseDateRange($request->input('accepted_task_date'));
            $query->whereHas('task', function ($q) use ($task_filter, $dtDateRange, $request) {
                if (!empty($dtDateRange[1])) {
                    // Both start and end dates are available
                    $query->whereBetween('accepted_date', [$dtDateRange[0], $dtDateRange[1]]);
                } else {
                    $inputDate = $request->input('accepted_task_date');
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                    // Only a single date is provided
                    $query->whereDate('accepted_date', $formattedDate);
                }
            });
        }



        if ($request->input('end_date')) {
            $dtDateRange = parseDateRange($request->input('end_date'));



            if (!empty($dtDateRange[1])) {
                // Both start and end dates are available
                $query->whereBetween('due_date', [$dtDateRange[0], $dtDateRange[1]]);
            } else {
                $inputDate = $request->input('end_date');
                $formattedDate = Carbon::createFromFormat('d/m/Y', $inputDate)->format('Y-m-d');
                // Only a single date is provided
                $query->whereDate('due_date', $formattedDate);
            }
        }



        // Handle the project filter
        if ($project = $request->input('project')) {
            $query->whereHas('task', function ($q) use ($project) {
                $q->where('project_id', $project); // Filter tasks by their project_id
            });
        }

        // Get the tasks in paginated chunks if necessary, or just all if you want to return everything
        $tasks = $query->get();
        // Return the data using DataTables, add custom columns
        return DataTables::of($tasks)

            ->addColumn('actions', function ($row) {
                // dd($row);
                $encryptedId = encrypt($row->task_id);
                // $satusData = TaskAssignee::where('')
                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";

                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket == 0 ? 'Task' : 'Ticket') : 'Task';
            })


            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "-";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }

    public function exportTotalTasks(Request $request)
    {
        $userId = auth()->user()->id;
        $user = auth()->user();
        $tasks = [];
        ini_set('memory_limit', '2048M');


        // Function to recursively retrieve the hierarchy
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

        $query = Task::query();

        // Apply filters
        if ($request->has('assignees') && !empty($request->assignees)) {
            $query->whereHas('assignees', function ($q) use ($request) {
                $q->whereIn('user_id', $request->assignees);
            });
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('task_status', $request->status);
        }
        if ($request->has('task') && $request->task != '') {
            $query->where('ticket', $request->task);
        }

        if ($request->has('dt_date') && $request->dt_date != '') {
            $startDateParts = explode(' to ', $request->dt_date);

            if (count($startDateParts) === 2) {
                $startDate = trim($startDateParts[0]);
                $endDate = trim($startDateParts[1]);

                // Specify the format when parsing the dates
                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

                $query->whereDate('start_date', '>=', $startDate)->whereDate('start_date', '<=', $endDate);
            }
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $dueDateParts = explode(' to ', $request->end_date);

            if (count($dueDateParts) === 2) {
                $startDate = trim($dueDateParts[0]);
                $endDate = trim($dueDateParts[1]);

                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

                $query->whereDate('due_date', '>=', $startDate)->whereDate('due_date', '<=', $endDate);
            }
        }
        if ($request->has('accepted_task_date') && $request->accepted_task_date != '') {
            $acceptedDateParts = explode(' to ', $request->accepted_task_date);

            if (count($acceptedDateParts) === 2) {
                $startDate = trim($acceptedDateParts[0]);
                $endDate = trim($acceptedDateParts[1]);

                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

                $query->whereDate('accepted_date', '>=', $startDate)->whereDate('accepted_date', '<=', $endDate);
            }
        }

        if ($request->has('created_by') && $request->created_by != '') {
            $query->where('created_by', $request->created_by);
        }
        if ($request->has('department') && $request->department != '') {
            $query->where('department_id', $request->department);
        }
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('start_date', $request->start_date);
        }

        if ($userId == 1 || $user->hasRole('Super Admin')) {
            $query->where('task_status', '!=', 2);
        } else {
            $query->whereIn('created_by', $addedUserIds)
                ->orWhereHas('assignees', function ($q) use ($addedUserIds) {
                    $q->whereIn('user_id', $addedUserIds);
                });
        }

        $tasks = $query->select('tasks.*')->get();

        return Excel::download(new TotalTasksExport($tasks), 'total_tasks.xlsx');
    }
    public function download($attachmentId)
    {

        // return redirect()->back()->with('success', 'Unable to document this right now');
        //21-06
        // $attachment = TaskAttachment::findOrFail($attachmentId);
        // $filePath = $attachment->file;
        // if (Storage::exists($filePath)) {

        //     return Storage::download($filePath, 'download.png');
        // } else {

        //     abort(404, 'File not found.');
        // }
        // 21-06



        $attachment = TaskAttachment::findOrFail($attachmentId);
        $filePath = $attachment->file;

        // Check if the file exists in storage
        if (Storage::exists($filePath)) {
            // Get the original filename from the file path
            $originalFilename = pathinfo($filePath, PATHINFO_BASENAME);

            // Download the file with a specific filename
            return Storage::download($filePath, $originalFilename);
        } else {
            abort(404, 'File not found.');
        }


    }
    public function getAll_team_and_mytask(Request $request)
    {
        $userId = auth()->user()->id;

        // Query 1: Tasks created by or assigned to the user with task_assignees.status = 1
        $my_task_query = Task::select('tasks.*') // Fetch all task fields for DataTables
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where(function ($query) use ($userId) {
                $query->where('tasks.created_by', $userId)
                    ->orWhere('task_assignees.user_id', $userId);
            })
            ->where('task_assignees.status', 1);

        // Query 2: Tasks assigned to the user and accepted, but not created by the user
        $taccepted_by_me_query = Task::select('tasks.*')
            ->whereHas('assignees', callback: function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            })
            ->where('tasks.created_by', '!=', $userId);

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
        $hierarchical_tasks_query = Task::select('tasks.*')
            ->where(function ($query) use ($addedUserIds, $userId) {
                $query->whereIn('created_by', $addedUserIds)
                    ->where('created_by', '!=', $userId) // Exclude tasks created by the logged-in user
                    ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
                        $q->whereIn('user_id', $addedUserIds)
                            ->where('user_id', '!=', $userId);
                    });
            });

        // Get the combined tasks data
        $final_tasks = $all_tasks
            ->union($hierarchical_tasks_query)
            ->get();
        if (!empty($request->search['value'])) {
            $final_tasks = Task::query();
            $searchTerm = $request->search['value'];
            $final_tasks->where(function ($query) use ($searchTerm) {
                $query->where('TaskNumber', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title', 'like', '%' . $searchTerm . '%');
                // Add other columns as needed
            });
        }
        // Return the data in a format compatible with DataTables
        return DataTables::of($final_tasks)

            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name ?? '-' : "-";
            })

            ->addColumn('task_Assign', function ($row) {
                return $row->users ? implode(', ', $row->users()->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")->pluck('full_name')->toArray()) : "-";
            })
            ->addColumn('task_status_name', function ($row) {
                return $row->taskStatus ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('project_name', function ($row) {
                return $row->project ? $row->project->project_name : "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department ? $row->department->department_name : "-";
            })
            ->addColumn('sub_department_name', function ($row) {
                return $row->sub_department ? $row->sub_department->sub_department_name : "-";
            })
            ->addColumn('created_by_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })
            ->addColumn('created_by_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('created_by_phone_no', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->addColumn('id', function ($row) {
                return $row->id;
            })

            ->rawColumns(['actions'])
            ->make(true);
    }
    public function getAll_team_task()
    {
        $userId = auth()->user()->id;

        // Get all users under the current user's report hierarchy
        $allUsers = User::where('report_to', $userId)
            ->orWhereIn('id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('users')
                    ->where('report_to', $userId);
            })->pluck('id')->toArray();

        // Merge current user with the team
        $addedUserIds = array_merge([$userId], $allUsers);

        // Use eager loading to optimize related model queries
        $teamTasks = Task::with(['creator', 'creator.department', 'creator.sub_department', 'taskStatus', 'project', 'department', 'sub_department', 'users'])
            ->where(function ($query) use ($addedUserIds, $userId) {
                // Simplify the where condition to avoid redundant checks
                $query->whereIn('created_by', $addedUserIds)
                    ->where('created_by', '!=', $userId)
                    ->orWhereHas('assignees', function ($q) use ($addedUserIds, $userId) {
                    $q->whereIn('user_id', $addedUserIds)
                        ->where('user_id', '!=', $userId);
                });
            });

        // Return the data using DataTables with added columns
        return DataTables::of($teamTasks)
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name ?? '-' : "-";
            })
            ->addColumn('task_Assign', function ($row) {
                return $row->users ? $row->users->pluck('full_name')->implode(', ') : "-";
            })
            ->addColumn('task_status_name', function ($row) {
                return $row->taskStatus ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('project_name', function ($row) {
                return $row->project ? $row->project->project_name : "-";
            })
            ->addColumn('department_name', function ($row) {
                return $row->department ? $row->department->department_name : "-";
            })
            ->addColumn('sub_department_name', function ($row) {
                return $row->sub_department ? $row->sub_department->sub_department_name : "-";
            })
            ->addColumn('created_by_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })
            ->addColumn('created_by_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('created_by_phone_no', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })
            ->filterColumn('task_number', function ($query, $keyword) {
                $query->where('task_number', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getStatuses()
    {
        try {
            $statuses = Status::where('status', 'on')->get();
            return response()->json($statuses);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function getProjects()
    {
        try {
            $projects = Project::get(); // Assuming there's a 'status' column for active projects
            return response()->json($projects);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getCreatedByOptions()
    {
        try {
            // Assuming you have a User model for created_by
            $createdByOptions = User::all()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name
                ];
            });
            return response()->json($createdByOptions);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function getDepartmentOptions()
    {
        try {
            $departmentOptions = Department::all();
            // dd($departmentOptions);
            return response()->json($departmentOptions);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function rejected_task(Request $request)
    {
        if ($request->ajax()) {
            // Get the rejected task assignees
            // $rejectedTasks = TaskAssignee::select(
            //     'task_assignees.*',  // Get all fields from task_assignees
            //     'tasks.description', // Description from the Task model
            //     'tasks.created_by',  // Created by from the Task model
            //     'tasks.project_id',  // Project ID from the Task model
            //     'tasks.task_status', // Task status from Task model
            //     'tasks.id', // Task status from Task model
            //     'users.first_name',  // First name of the user (assignee)
            //     'users.id',  // First name of the user (assignee)
            //     'users.last_name',   // Last name of the user (assignee)
            //     'projects.project_name', // Project name
            //     'task_assignees.remark as rejection_reason' // Rejection reason from task_assignees
            // )
            //     ->join('tasks', 'task_assignees.task_id', '=', 'tasks.id')  // Join with tasks
            //     ->join('users', 'task_assignees.user_id', '=', 'users.id')  // Join with users (assignees)
            //     ->join('projects', 'tasks.project_id', '=', 'projects.id')  // Join with projects
            // ->where('task_assignees.status', 2)  // Filter for rejected status (2)
            // ->orderBy('task_assignees.id', 'desc')  // Order by task_assignees ID in descending order
            // ->get();
            // dd($rejectedTasks);

            $rejectedTasks = TaskAssignee::where('status', 2)  // Filter for rejected status (2)
                ->orderBy('id', 'desc')  // Order by task_assignees ID in descending order
                ->get();
            // Check if the current user is admin (id = 1)
            if (auth()->user()->id == 1) {
                // Admin can view all rejected tasks
                $rejectedTasks = $rejectedTasks;
            } else {
                // Non-admin users can only see tasks they created
                $rejectedTasks = $rejectedTasks->where('created_by', auth()->user()->id);
            }

            return datatables()->of($rejectedTasks)
                ->addColumn('actions', function ($row) {
                    $encryptedId = encrypt($row->task_id);

                    $updateButton = '';
                    $deleteButton = '';
                    $acceptButton = '';
                    if (auth()->user()->id == '1') {
                        if ($row->status == 0) {
                            $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                        }
                        $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                        $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                    } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                        $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                    } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                        $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                        $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                    }

                    // $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning btn-sm me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    // $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Delete Task' class='btn-sm btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                    $viewButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='View Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                    $buttons = $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewButton;
                    return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
                })
                ->addColumn('created_by_username', function ($row) {
                    return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
                })
                ->addColumn('Task_number', function ($row) {
                    return $row->task_number ?? "-";
                })
                ->addColumn('Task_Ticket', function ($row) {
                    return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
                })
                ->addColumn('description', function ($row) {
                    return $row->task && $row->task->description ? $row->task->description : '-';
                })
                ->addColumn('subject', function ($row) {
                    return $row->task && $row->task->subject ? $row->task->subject : '-';
                })
                ->addColumn('title', function ($row) {
                    return $row->task && $row->task->title ? $row->task->title : '-';
                })
                ->addColumn('Task_assign_to', function ($row) {
                    return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
                })
                ->addColumn('status', function ($row) {
                    return $row->task_status ? $row->task->taskStatus->status_name : "-"; // Assuming 'task_status' is on the Task model
                })
                ->addColumn('Created_Date', function ($row) {
                    return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
                })
                ->addColumn('due_date', function ($row) {
                    return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
                })

                ->addColumn('close_date', function ($row) {
                    return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
                })
                ->addColumn('completed_date', function ($row) {
                    return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
                })
                ->addColumn('accepted_date', function ($row) {
                    return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
                })
                ->addColumn('project', function ($row) {
                    return $row->task && $row->task->project ? $row->task->project->project_name : '-';
                })
                ->addColumn('department', function ($row) {
                    return $row->department && $row->department_data ? $row->department_data->department_name : '-';
                })
                ->addColumn('sub_department', function ($row) {
                    return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
                })
                ->addColumn('creator_department', function ($row) {
                    return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
                })
                ->addColumn('creator_sub_department', function ($row) {
                    return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
                })
                ->addColumn('creator_phone', function ($row) {
                    return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
                })
                ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
                ->make(true);
        }

        // Return the view if not an AJAX request
        return view('content.apps.task.rejected_tasks'); // Make sure this view path is correct
    }

    public function importTasks(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        // Import the file
        Excel::import(new TaskUpdateImport, $request->file('file'));

        return redirect()->back()->with('success', 'Tasks updated successfully.');
    }
    private function getHierarchy($userId, &$allUsers, &$addedUserIds)
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
    public function getAll_mytask(Request $request)
    {
        $userId = auth()->user()->id;

        // Query using TaskAssignee model
        $tasks = TaskAssignee::select('task_assignees.*', 'tasks.title', 'tasks.description', 'tasks.subject')
            ->leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')

            ->where('user_id', $userId)  // Focus on task assignees
            ->where('status', '!=', 2)  // Ensure the task is not deleted (assuming status 2 is deleted)
            ->with([
                'task',  // Load the related task
                'creator',
                'department_data',
                'sub_department_data',
                'task.attachments',
                'task.assignees' => function ($query) {
                    $query->select('task_id', 'status', 'remark'); // Customize as needed
                },
                'task.creator',  // Task creator
                'task.taskStatus',  // Task status
                'task.project',  // Task project
                'task.department',  // Task department
                'task.sub_department',  // Task sub-department
                'task.comments'  // Task comments
            ])
            ->whereHas('task', function ($query) use ($userId) {
                $query->where('created_by', $userId)  // Ensure the task was created by the current user
                    ->havingRaw('COUNT(task_assignees.user_id) = 1');  // Ensure task has only one assignee
            });


        if ($request->has('search') && $request->get('search')['value']) {
            $search = $request->get('search')['value'];
            $tasks->where(function ($query) use ($search) {
                $query->where('tasks.title', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.description', 'LIKE', "%{$search}%")
                    ->orWhere('tasks.subject', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.task_number', 'LIKE', "%{$search}%")
                    ->orWhere('task_assignees.remark', 'LIKE', "%{$search}%")
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('department_data', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('department_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('sub_department_data', function ($subDepartmentQuery) use ($search) {
                        $subDepartmentQuery->where('sub_department_name', 'LIKE', "%{$search}%");
                    });
            });
        }
        return DataTables::of($tasks->get())
            ->addColumn('actions', function ($row) {
                // dd($row->task_id);
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $deleteButton = '';
                $acceptButton = '';
                if (auth()->user()->id == '1') {
                    if ($row->status == 0) {
                        $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    }
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    // $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1 accept-task' data-id='$encryptedId' data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task'><i class='ficon' data-feather='check-circle'></i></a>";

                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                } elseif ($row->user_id == auth()->user()->id || $row->created_by == auth()->user()->id) {
                    $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                }
                // $updateButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task' class='btn-sm btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "' target='_blank'><i class='ficon' data-feather='edit'></i></a>";
                // $deleteButton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='delete Task' class='btn-sm btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
                $viewbutton = "<a data-bs-toggle='tooltip' data-bs-placement='top' title='view Task' class='btn-sm btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
                return "<div class='d-flex justify-content-between'>" . $updateButton . " " . $acceptButton . " " . $deleteButton . " " . $viewbutton . "</div>";
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task_number ?? "-";
            })
            ->addColumn('Task_Ticket', function ($row) {
                return $row->task ? ($row->task->ticket ? $row->task->ticket : 'Task') : 'Task';
            })
            ->addColumn('description', function ($row) {
                return $row->task && $row->task->description ? $row->task->description : '-';
            })

            ->addColumn('subject', function ($row) {
                return $row->task && $row->task->subject ? $row->task->subject : '-';
            })
            ->addColumn('title', function ($row) {
                return $row->task && $row->task->title ? $row->task->title : '-';
            })
            ->addColumn('Task_assign_to', function ($row) {
                return $row->user_id && $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })

            ->addColumn('status', function ($row) {
                return $row->task_status ? $row->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? \Carbon\Carbon::parse($row->task->created_at)->format('d/m/Y') : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? \Carbon\Carbon::parse($row->task->start_date)->format('d/m/Y') : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            })

            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? Carbon::parse($row->task->close_date)->format('d/m/Y') : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? Carbon::parse($row->task->completed_date)->format('d/m/Y') : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->accepted_date ? Carbon::parse($row->accepted_date)->format('d/m/Y') : '-';
            })
            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->department && $row->department_data ? $row->department_data->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->sub_department && $row->sub_department_data ? $row->sub_department_data->sub_department_name : '-';
            })
            ->addColumn('creator_department', function ($row) {
                return $row->creator && $row->creator->department ? $row->creator->department->department_name : '-';
            })

            ->addColumn('creator_sub_department', function ($row) {
                return $row->creator && $row->creator->sub_department ? $row->creator->sub_department->sub_department_name : '-';
            })
            ->addColumn('creator_phone', function ($row) {
                return $row->creator && $row->creator->phone_no ? $row->creator->phone_no : '-';
            })

            ->rawColumns(['actions', 'title', 'creator_phone', 'creator_sub_department', 'creator_department', 'sub_department', 'department', 'project', 'accepted_date', 'completed_date', 'close_date', 'due_date', 'start_date', 'status', 'Task_assign_to', 'subject', 'description', 'Task_Ticket', 'created_by_username'])
            ->make(true);
    }
    public function markAsCompleted(Request $request, $subtaskId)
    {
        try {
            // Find the subtask by ID
            $subtask = TaskAssignee::find($subtaskId);

            // Check if the subtask exists
            if (!$subtask) {
                return response()->json(['success' => false, 'message' => 'Subtask not found'], 404);
            }

            // Update the status of the subtask to 'Completed'
            $subtask->task_status = '4';  // Or use a status code if applicable
            $subtask->save();

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Subtask marked as completed']);
        } catch (\Exception $e) {
            // Catch any exceptions and return error message
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeUser(Request $request, $subtaskId)
    {
        // Find the subtask by ID
        // dd($subtaskId);
        $subtask = TaskAssignee::find($subtaskId);
        // dd($subtask->creator);
        // Check if the authenticated user is the creator of the subtask
        if ($subtask->creator->id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the user_id from the request
        $userId = $request->input('user_id');
        // dd($userId);
        // Initialize TaskAssignee instance
        $taskAssignee = new TaskAssignee();

        // Soft delete the user from the task
        $success = $taskAssignee->removeUserFromTask($subtaskId, $userId);

        if ($success) {
            return response()->json(['success' => 'User removed successfully']);
        }

        return response()->json(['error' => 'Failed to remove user'], 400);
    }
    public function reopen($id, Request $request)
    {
        try {
            // Find the subtask by its ID
            $subtask = TaskAssignee::find($id);
            // dd($subtask);
            if ($subtask) {
                // Get task and user data
                $task = $subtask->task; // Assuming a 'task' relation exists
                $user = $subtask->user; // Assuming a 'user' relation exists
                // dd($task, $user);
                // Update the subtask status
                $subtask->task_status = $request->status;
                $subtask->reopen_date = now();
                $subtask->reopen_by = auth()->user()->id;
                // dd($subtask);
                $subtask->save();

                // dd($request->all());
                $reopenReason = new ReopenReason();
                $reopenReason->reason = $request->reason;  // Reopen reason from the frontend
                $reopenReason->reopen_date = now();
                $reopenReason->reopen_by = auth()->user()->id;
                $reopenReason->user_id = $user->id;
                $reopenReason->created_by = auth()->user()->id;
                $reopenReason->created_at = now();
                $reopenReason->save();
                // Construct the task view URL (replace with your actual URL generation logic)
                $taskViewUrl = route('app-task-view', ['encrypted_id' => encrypt($task->id)]); // Assuming a route 'task.view' exists

                // Create the notification message
                $message = 'Your task ' . $task->id . ' has been reopened.<br>
                    <a class="btn-sm btn-success me-1 mt-1" href="' . $taskViewUrl . '">View Task</a>';

                // Create notification for the user
                createNotification(
                    $user->id, // The user ID receiving the notification
                    $task->id, // The task ID
                    $message,   // The notification message
                    'Created'  // Status of the notification
                );

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Subtask not found']);
        } catch (\Exception $e) {
            dd($e->getMessage());

            // Return a response with the error message
            return response()->json(['success' => false, 'message' => 'An error occurred, please try again later.']);
        }
    }

    public function editSubtask(TaskAssignee $subtask)
    {
        $statuses = Status::all();  // Fetch the statuses from the database
        $task = $subtask->task;

        // Check if the task status has the 'disabled' property in the database
        return response()->json([
            'success' => true,
            'subtask' => [
                'due_date' => $subtask->due_date ? $subtask->due_date : null,
                'status' => $subtask->task_status,
            ],
            'statuses' => $statuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'displayname' => $status->displayname,
                    'disabled' => $status->disabled,  // Ensure 'disabled' is included
                ];
            }),
            'task' => $task
        ]);
    }




    // Method to update the subtask data
    public function updateSubtask(Request $request, TaskAssignee $subtask)
    {
        // dd($request->all());
        // Start by updating the subtask with the new data
        $subtask->due_date = $request->due_date;
        $subtask->task_status = $request->status; // Assuming task_status is an integer

        // Save the subtask
        if (!$subtask->save()) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error updating the subtask.',
            ], 500);
        }



        $comment = new Comments(); // Assuming you have a Comment model
        $comment->comment = $request->comment;
        $comment->task_id = $subtask->task_id; // Assuming the comment is related to a task
        $comment->created_by = auth()->id(); // The ID of the currently authenticated user
        $comment->save();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Subtask updated successfully.',
            'subtask' => $subtask, // Optionally, return the updated subtask data
        ]);
    }
    public function checkAndCreateTasks()
    {
        // Get today's date
        $today = Carbon::today()->toDateString();

        // Get all recurring tasks where start_date is today
        $tasksToCreate = RecurringTask::whereDate('start_date', $today)->get();

        foreach ($tasksToCreate as $recurringTask) {
            if ($recurringTask->is_sub_task == null) {
                $TempAttachments = RecursiveTaskAttachment::where('task_id', $recurringTask->id)->get();
            } else {
                $TempAttachments = RecursiveTaskAttachment::where('task_id', $recurringTask->is_sub_task)->get();
            }

            // Check if a task has already been created today for the recurring task
            $existingTask = Task::where('ticket', $recurringTask->ticket)
                ->whereDate('created_at', $today)
                ->first();

            // If an existing task is found, skip creation for this recurring task
            if ($existingTask) {
                continue; // Skip this task and move to the next recurring task
            }

            // Prepare task data
            $taskData = [
                'ticket' => $recurringTask->ticket,
                'title' => $recurringTask->title,
                'description' => $recurringTask->description,
                'subject' => $recurringTask->subject,
                'project_id' => $recurringTask->project_id,
                'start_date' => $recurringTask->start_date,
                'due_date' => $recurringTask->due_date,
                'priority_id' => $recurringTask->priority_id,
                'task_status' => $recurringTask->task_status,
                'created_by' => auth()->user()->id,
                'is_recursive' => 1,
            ];

            // Handle status-specific fields
            if ($recurringTask->task_status == 4) {
                $taskData['completed_date'] = now();
            }
            if ($recurringTask->task_status == 7) {
                $taskData['close_date'] = now();
            }

            // Create the task
            $task = Task::create($taskData);

            // Store the recursive_task_id on the Task table
            $task->recursive_task_id = $recurringTask->id;
            $task->save();

            // Create task attachments
            foreach ($TempAttachments as $attachment) {
                TaskAttachment::create([
                    'task_id' => $task->id,
                    'file' => $attachment->file,
                ]);
            }

            // Get user IDs from the recurring task
            $userIds = explode(',', $recurringTask->task_assignes);

            // Assign task numbers and update pivot table
            $startingTaskNumber = 1; // Starting task number for each user
            foreach ($userIds as $index => $userId) {
                $taskNumber = $task->id . '-' . str_pad($startingTaskNumber + $index, 2, '0', STR_PAD_LEFT); // Increment task number per user
                $user = User::find($userId);

                // Prepare user-specific data
                $departmentId = $user->department_id;
                $subdepartment = $user->subdepartment;

                // Check if the created_by user is the same as the assigned user (user_id)
                $status = (auth()->user()->id == $userId) ? 1 : 0; // If they are the same, set status to 1, otherwise 0

                // Update pivot with user-specific task number and additional details
                TaskAssignee::create([
                    'task_id' => $task->id,         // Store the task_id
                    'user_id' => $userId,           // Store the user_id
                    'status' => $status,            // Set the status based on the created_by vs user_id check
                    'task_status' => $recurringTask->task_status,
                    'task_number' => $taskNumber,
                    'due_date' => $recurringTask->due_date,
                    'department' => $departmentId,
                    'sub_department' => $subdepartment,
                    'created_by' => auth()->user()->id,
                    'created_at' => now(),
                ]);
            }

            // Update the last task number for the task
            $task->last_task_number = $taskNumber;
            $task->save();

            // Send notifications to users
            $this->sendTaskNotifications($task, $userIds);

            // Soft delete the recurring task and mark it as completed
            $recurringTask->is_completed = 1;
            $recurringTask->save();
            // $recurringTask->delete();  // Soft delete from RecurringTask table
        }

        // Redirect with success message
        return redirect()->route('app-task-list')->with('success', 'Recurring tasks for today created successfully.');
    }





    // Helper method to send notifications
    protected function sendTaskNotifications($task, $userIds)
    {
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            // Send notification to the user
            createNotification(
                $user->id,
                $task->id,
                'New task ' . $task->id . ' assigned to you.',
                'Created'
            );
        }
    }

}
