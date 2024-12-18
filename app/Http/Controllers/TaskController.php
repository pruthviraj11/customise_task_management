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
use App\Models\TaskAttachment;
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

    public function index()
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

        $type = last(explode('-', request()->route()->getName()));

        // dd($type);

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


        return view('content.apps.task.list', compact('data', 'type'));
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

    }

    public function view($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $task = $this->taskService->gettask($id);

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
            return view('content.apps.task.view', compact('page_data', 'hasAcceptedTask', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId'));
        } catch (\Exception $error) {
            // dd($error->getMessage());
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
        // dd("sd");
        // dd($request->all());
        $request->validate([
            'comment' => 'required|string',
            'task_id' => 'required|exists:tasks,id',
        ]);

        $comment = new Comments();
        $comment->comment = $request->input('comment');
        $comment->task_id = $request->input('task_id');
        $comment->created_by = Auth::id();
        $comment->save();
        // return redirect()->route("app-task-list")->with('success', 'Comment added successfully!');
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
        $userId = auth()->user()->id;

        // Fetch tasks assigned to the user but created by the authenticated user
        $tasks = TaskAssignee::where('created_by', $userId)
            ->whereDoesntHave('user', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        // Apply search filter if search term is provided
        if (!empty($request->search['value'])) {
            $searchTerm = $request->search['value'];
            $tasks->where(function ($query) use ($searchTerm) {
                $query->where('task_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title', 'like', '%' . $searchTerm . '%');
            });
        }

        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
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
            ->addColumn('created_by_username', function ($row) {
                return $row->creator ? $row->creator->first_name . " " . $row->creator->last_name : "-";
            })
            ->addColumn('Task_number', function ($row) {
                return $row->task ? $row->task->task_number ?? "-" : "-";
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
                return $row->user ? $row->user->first_name . " " . $row->user->last_name : "ABC";
            })
            ->addColumn('status', function ($row) {
                return $row->task && $row->task->task_status ? $row->task->taskStatus->status_name : "-";
            })
            ->addColumn('Created_Date', function ($row) {
                return $row->task && $row->task->created_at ? $row->task->created_at : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? $row->task->start_date : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->task && $row->task->due_date ? $row->task->due_date : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? $row->task->close_date : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
            })
            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->task && $row->task->department ? $row->task->department->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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
            ->rawColumns(['actions'])
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
    public function getAll_requested()
    {
        $user = auth()->user();

        if (Auth()->user()->id == 1) {

            $tasks = TaskAssignee::whereHas('task', function ($query) {

                $query->whereHas('assignees', function ($query) {
                    $query->where('status', 0);
                })
                    ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            })
                ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
                ->get();
        } else {

            $tasks = TaskAssignee::whereHas('task', function ($query) use ($user) {

                $query->whereHas('assignees', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('status', 0);
                })
                    ->where('task_status', '!=', 7); // Use 'task_status' from tasks table
            })
                ->whereNull('task_assignees.deleted_at')  // Ensure the assignee is not deleted
                ->get();
        }


        return DataTables::of($tasks)->addColumn('actions', function ($row) {


            $encryptedId = encrypt($row->task->id);


            $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";


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
                return $row->task && $row->task->created_at ? $row->task->created_at : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? $row->task->start_date : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->task && $row->task->due_date ? $row->task->due_date : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? $row->task->close_date : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->task && $row->task->department ? $row->task->department->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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
            ->rawColumns(['actions'])
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
            $tasks = TaskAssignee::whereHas('task', function ($query) {
                $query->where('status', '1'); // Assuming 'status' is in the Task model
            });
        } else {
            $tasks = TaskAssignee::whereHas('task', function ($query) use ($user) {
                $query->where('status', '1'); // Assuming 'status' is in the Task model
            })->where('user_id', $user->id); // Ensure we filter by the logged-in user
        }

        // Apply search filter if provided
        if (!empty($request->search['value'])) {
            $searchTerm = $request->search['value'];
            $tasks->where(function ($query) use ($searchTerm) {
                $query->whereHas('task', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('TaskNumber', 'like', '%' . $searchTerm . '%')
                        ->orWhere('ticket', 'like', '%' . $searchTerm . '%')
                        ->orWhere('title', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
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
                return $row->task && $row->task->created_at ? $row->task->created_at : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? $row->task->start_date : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->task && $row->task->due_date ? $row->task->due_date : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? $row->task->close_date : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
            })
            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->task && $row->task->department ? $row->task->department->department_name : '-';
            })
            ->addColumn('sub_department', function ($row) {
                return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function requestedToUsTasks($user_id, $status_id, $type)
    {
        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {
            // Fetch tasks based on user ID and status ID
            $tasks = TaskAssignee::where('user_id', $user)->where('status', '0')->where('created_by', $user_id)->get();
        }elseif($type == 'requested_by_me'){
            $tasks = TaskAssignee::where('user_id', $user_id)->where('status', '0')->where('created_by', $user)->get();
        }elseif($type == 'total_task'){
            $tasks = TaskAssignee::where('user_id', $user)->where('status', '0')->count();
            dd($tasks);

        }

        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasks,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }
    public function requestedToUsStatusTasks($user_id, $status_id, $type)
    {
        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {
            $tasks = TaskAssignee::where('user_id', $user)->where('task_status', $status_id)->where('created_by', $user_id)->get();

        }elseif($type = 'requested_by_me'){
            $tasks = TaskAssignee::where('user_id', $user)->where('task_status', $status_id)->where('created_by', $user)->get();

        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasks,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }

    public function requestedToUsPendingTasks($user_id, $status_id, $type)
    {
        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {
            // Fetch tasks based on user ID and status ID
            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 5, 6])
                ->where('created_by', $user_id)
                ->get();
        }elseif($type == 'requested_by_me'){
            $tasks = TaskAssignee::where('user_id', $user_id)
            ->whereIn('task_status', [1, 3, 5, 6])
            ->where('created_by', $user)
            ->get();
        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasks,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }
    public function requestedToUsOverDuesTasks($user_id, $status_id, $type)
    {
        $user = auth()->user()->id;
        $cdate = date("Y-m-d");
        if ($type == 'requested_to_us') {
            // Fetch tasks based on user ID and status ID
            $due_tasks = $due_tasks = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->get();
            $tasksData = [];
            foreach ($due_tasks as $due_task) {
                $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->get();
                foreach ($countTotalTask as $task) {
                    $tasksData[] = $task; // Add the task to the array
                }
            }
        }elseif($type == 'requested_by_me'){

            $due_tasks = $due_tasks = TaskAssignee::where('user_id', $user_id)
            ->where('created_by', $user)
            ->whereNotIn('task_status', [4, 7])
            ->get();
        $tasksData = [];
        foreach ($due_tasks as $due_task) {
            $countTotalTask = Task::where('id', $due_task->task_id)->whereDate('due_date', '<', $cdate)->get();
            foreach ($countTotalTask as $task) {
                $tasksData[] = $task; // Add the task to the array
            }
        }
        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasksData,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }

    public function requestedToUsTodayDuesTasks($user_id, $status_id, $type)
    {
        $user = auth()->user()->id;
        $cdate = date("Y-m-d");
        if ($type == 'requested_to_us') {

            // Fetch tasks based on user ID and status ID
            $today_tasks = TaskAssignee::where('user_id', $user)
                ->where('created_by', $user_id)
                ->whereNotIn('task_status', [4, 7])
                ->get();

            $tasksData = [];
            foreach ($today_tasks as $today_task) {
                $countTotalTask = Task::where('id', $today_task->task_id)->where('due_date', '=', $cdate)->get();
                foreach ($countTotalTask as $task) {
                    $tasksData[] = $task; // Add the task to the array
                }
            }
        }elseif($type == 'requested_by_me'){
            $today_tasks = TaskAssignee::where('user_id', $user_id)
            ->where('created_by', $user)
            ->whereNotIn('task_status', [4, 7])
            ->get();

        $tasksData = [];
        foreach ($today_tasks as $today_task) {
            $countTotalTask = Task::where('id', $today_task->task_id)->where('due_date', '=', $cdate)->get();
            foreach ($countTotalTask as $task) {
                $tasksData[] = $task; // Add the task to the array
            }
        }
        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasksData,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }


    public function requestedToUsFinishedTasks($user_id, $status_id, $type)
    {

        $user = auth()->user()->id;
        // Fetch tasks based on user ID and status ID
        if ($type == 'requested_to_us') {

            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', ['4', '7'])
                ->where('created_by', $user_id)
                ->get();
        }elseif($type == 'requested_by_me'){
            $tasks = TaskAssignee::where('user_id', $user_id)
            ->whereIn('task_status', ['4', '7'])
            ->where('created_by', $user)
            ->get();
        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasks,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
    }

    public function requestedToUsTotalTasks($user_id, $status_id, $type)
    {

        $user = auth()->user()->id;
        if ($type == 'requested_to_us') {

            // Fetch tasks based on user ID and status ID
            $tasks = TaskAssignee::where('user_id', $user)
                ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
                ->where('created_by', $user_id)
                ->get();
        }elseif($type == 'requested_by_me'){
            $tasks = TaskAssignee::where('user_id', $user_id)
            ->whereIn('task_status', [1, 3, 4, 5, 6, 7])
            ->where('created_by', $user)
            ->get();
        }
        // Pass data to a view (or return as JSON if it's an API)
        return view('tasks.show', [
            'tasks' => $tasks,
            'user_id' => $user_id,
            'status_id' => $status_id,
            'type' => 'requested_by_me'
        ]);
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
                $task_assignee->update(['status' => 1]);
            }
            $task->accepted_date = now()->format('Y-m-d H:i:s');
            $task->save();

            return redirect()->route("app-task-requested")->with('success', 'Task Accepted Successfully');
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-task-requested")->with('error', 'Error while Accepting Task');
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
            // Fetch project, priority, and status
            $project = Project::where('id', $request->get('project_id'))->first();
            $priority = Priority::where('id', $request->get('priority_id'))->first();
            $status = Status::where('id', $request->get('task_status'))->first();

            // Prepare task data
            $taskData = [
                'ticket' => $request->get('task_type') == '1' ? 1 : 0,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'subject' => $request->get('subject'),
                'project_id' => $request->get('project_id'),
                'project_name' => $project->project_name,
                'start_date' => $request->get('start_date'),
                'due_date' => $request->get('due_date'),
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
                // Update pivot with user-specific task number
                $task->users()->updateExistingPivot($userId, [
                    'status' => 0,
                    'task_status' => $request->get('task_status'),
                    'task_number' => $taskNumber,
                    'due_date' => $request->get('due_date'),
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
                $creator = 1;
                $getTaskComments = '';
            } else {
                $task = $this->taskService->gettaskAssigne($id)->first();
                $getTaskComments = Task::where('id', $task->task_id)->first();
                // dd($getTaskComments);
                $creator = 0;
                // dd($task);
            }

            // dd($task);


            $SubTaskData = TaskAssignee::where('task_id', $task->id)
                ->where(function ($query) {
                    $query->where('created_by', Auth::user()->id)
                        ->orWhere('user_id', Auth::user()->id); // Check for either created_by or user_id
                })
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
                return view('.content.apps.task.create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'Maintask', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'SubTaskData', 'getTaskComments'));
            } else {
                return view('.content.apps.task.assigne-create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'Maintask', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'SubTaskData', 'getTaskComments'));
                return view('.content.apps.task.assigne-create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'Maintask', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId', 'SubTaskData', 'getTaskComments'));

            }
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

            $task = Task::findOrFail($id);

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
                    'due_date' => $request->get('due_date'),
                    'priority_id' => $request->get('priority_id'),
                    'task_status' => $request->get('task_status'),
                    'updated_by' => auth()->user()->id,
                ];
                // dd($taskData);

                // Handle task status specific fields (completed_date and close_date)
                if ($request->get('task_status') == 4) {
                    // dd("Completed");
                    $taskData['completed_date'] = now();
                } else {
                    $taskData['completed_date'] = null;
                }

                if ($request->get('task_status') == 7) {
                    // dd("Completed");

                    $taskData['close_date'] = now();
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
                    'due_date' => $request->get('due_date'),
                    'task_status' => $request->get('task_status'),
                ];
                // dd($taskData);
                // Handle task status specific fields (completed_date and close_date)
                if ($request->get('task_status') == 4) {
                    $taskData['completed_date'] = now();
                    $taskData['completed_by'] = auth()->user()->id;
                } else {
                    $taskData['completed_date'] = null;
                }

                if ($request->get('task_status') == 7) {
                    $taskData['close_date'] = now();
                    $taskData['close_by'] = auth()->user()->id;
                }

                if ($request->get('closed') == 'on' && $task->created_by == auth()->user()->id) {
                    $taskData['task_status'] = 7;
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

            // Check if any comment was provided and save it
            // dd($$request->comment);
            if ($request->comment != '') {
                $comment = new Comments();
                $comment->comment = $request->input('comment');
                $comment->task_id = $request->input('task_id');
                $comment->created_by = auth()->id();
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
                $q->where('user_id', $userId)
                    ->whereHas('user', function ($q) {
                        // Ensure the user is not deleted (i.e., deleted_at is null)
                        $q->whereNull('deleted_at');
                    });
            });
        }

        // Apply filters before executing the query to reduce unnecessary data retrieval
        if ($task_filter = $request->input('task')) {
            $query->where('tasks.ticket', $task_filter);
        }

        if ($department_filter = $request->input('department')) {
            $query->where('tasks.department_id', $department_filter);
        }

        if ($created_by = $request->input('created_by')) {
            $query->where('tasks.created_by', $created_by);
        }

        if ($assignees = $request->input('assignees')) {
            $query->whereHas('assignees', function ($q) use ($assignees) {
                $q->whereIn('user_id', $assignees);
            });
        }

        if ($status = $request->input('status')) {
            $query->where('tasks.task_status', $status);
        }

        // Date filters
        if ($dtDateRange = parseDateRange($request->input('dt_date'))) {
            $query->whereBetween('tasks.start_date', $dtDateRange);
        }

        if ($acceptedDateRange = parseDateRange($request->input('accepted_task_date'))) {
            $query->whereBetween('tasks.accepted_date', $acceptedDateRange);
        }

        if ($dueDateRange = parseDateRange($request->input('end_date'))) {
            $query->whereBetween('tasks.due_date', $dueDateRange);
        }

        if ($project = $request->input('project')) {
            $query->where('tasks.project_id', $project);
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
                return $row->task && $row->task->created_at ? $row->task->created_at : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? $row->task->start_date : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->task && $row->task->due_date ? $row->task->due_date : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? $row->task->close_date : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->task && $row->task->department ? $row->task->department->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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




            ->rawColumns(['actions'])
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
                    $acceptButton = '';
                    if ($row->status == 0 && $row->user_id == auth()->user()->id) {
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
                    return $row->task && $row->task->created_at ? $row->task->created_at : '-';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->task && $row->task->start_date ? $row->task->start_date : '-';
                })
                ->addColumn('due_date', function ($row) {
                    return $row->task && $row->task->due_date ? $row->task->due_date : '-';
                })
                ->addColumn('close_date', function ($row) {
                    return $row->task && $row->task->close_date ? $row->task->close_date : '-';
                })
                ->addColumn('completed_date', function ($row) {
                    return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
                })
                ->addColumn('accepted_date', function ($row) {
                    return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
                })
                ->addColumn('project', function ($row) {
                    return $row->task && $row->task->project ? $row->task->project->project_name : '-';
                })
                ->addColumn('department', function ($row) {
                    return $row->task && $row->task->department ? $row->task->department->department_name : '-';
                })
                ->addColumn('sub_department', function ($row) {
                    return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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
                ->rawColumns(['actions'])
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
    public function getAll_mytask()
    {
        $userId = auth()->user()->id;

        // Query using TaskAssignee model
        $tasks = TaskAssignee::where('user_id', $userId)  // Focus on task assignees
            ->where('status', '!=', 2)  // Ensure the task is not deleted (assuming status 2 is deleted)
            ->with([
                'task',  // Load the related task
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

        return DataTables::of($tasks)
            ->addColumn('actions', function ($row) {
                // dd($row->task_id);
                $encryptedId = encrypt($row->task_id);

                $updateButton = '';
                $acceptButton = '';
                if ($row->status == 0 && $row->user_id == auth()->user()->id) {
                    $acceptButton = "<a class='btn-sm btn-success btn-sm me-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Accept Task' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
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
                return $row->task && $row->task->created_at ? $row->task->created_at : '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->task && $row->task->start_date ? $row->task->start_date : '-';
            })
            ->addColumn('due_date', function ($row) {
                return $row->task && $row->task->due_date ? $row->task->due_date : '-';
            })
            ->addColumn('close_date', function ($row) {
                return $row->task && $row->task->close_date ? $row->task->close_date : '-';
            })
            ->addColumn('completed_date', function ($row) {
                return $row->task && $row->task->completed_date ? $row->task->completed_date : '-';
            })
            ->addColumn('accepted_date', function ($row) {
                return $row->task && $row->task->accepted_date ? $row->task->accepted_date : '-';
            })

            ->addColumn('project', function ($row) {
                return $row->task && $row->task->project ? $row->task->project->project_name : '-';
            })
            ->addColumn('department', function ($row) {
                return $row->task && $row->task->department ? $row->task->department->department_name : '-';
            })

            ->addColumn('sub_department', function ($row) {
                return $row->task && $row->task->sub_department ? $row->task->sub_department->sub_department_name : '-';
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

            ->rawColumns(['actions'])
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




}
