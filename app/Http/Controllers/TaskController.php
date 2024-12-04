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
use App\Models\Priority;
use App\Models\Comments;
use App\Models\TaskAttachment;
use App\Models\TaskAssignee;
use Illuminate\Support\Facades\Storage;
use App\Services\RoleService;
use App\Services\TaskService;
use App\Services\StatusService;
use Spatie\Permission\Models\Permission;
use App\Mail\TaskCreatedMail;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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

        // Permission::create(['name' => 'task-list', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-create', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-edit', 'guard_name' => 'web', 'module_name' => 'Task']);
        // Permission::create(['name' => 'task-delete', 'guard_name' => 'web', 'module_name' => 'Task']);

    }

    public function index()
    {
        $type = last(explode('-', request()->route()->getName()));
        // echo $type;
        // die;
        $data['total_department'] = Task::count();
        $data['department'] = Task::get();
        // dd($type);

        return view('content.apps.task.list', compact('data', 'type'));
    }

    public function view($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $task = $this->taskService->gettask($id);
            // dd($task);
            $page_data['page_title'] = "Task";
            $page_data['form_title'] = "Edit Task";

            $projects = Project::where('status', 'on')->get();
            $departments = Department::where('status', 'on')->get();
            $Subdepartments = SubDepartment::where('status', 'on')->get();
            $Status = Status::where('status', 'on')->get();
            $Prioritys = Priority::where('status', 'on')->get();
            $users = User::where('status', '1')->get();
            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;

            return view('content.apps.task.view', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while editing Task');
        }


    }

    public function kanban()
    {
        $pageConfigs = [
            'pageHeader' => true,
            'pageClass' => 'kanban-application',
        ];
        $type = last(explode('-', request()->route()->getName()));
        //echo $type;die;
        $data['total_department'] = Task::count();
        $data['department'] = Task::get();
        return view('content.apps.task.kanban', compact('data', 'type', 'pageConfigs'));
    }

    public function getAll()
    {
        if (auth()->user()->id == 1) {
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
            $updateButton = "<a class='btn btn-warning btn-sm me-1 '  href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

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

            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_mytask()
    {
        // dd('jklhsdfdsf');
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
            });
        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning btn-sm me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

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
            $updateButton = "<a class='btn btn-warning btn-sm' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete btn-sm' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

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

    public function getAll_accepted_by_me()
    {
        $userId = auth()->user()->id;

        // Retrieve tasks where the user is either the creator or assigned
        $tasks = Task::select('tasks.*')->leftjoin('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.status', 1)
            ->where('tasks.created_by', '!=', $userId);
        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning btn-sm' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete btn-sm mx-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_assign_by_me()
    {
        $userId = auth()->user()->id;


        $tasks = DB::table('tasks')
            ->where('created_by', '=', $userId)
            ->whereNotExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('task_assignees')
                    ->whereRaw('tasks.id = task_assignees.task_id')
                    ->where('user_id', '=', $userId);
            });


        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
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
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAllForView()
    {
        $status = $this->statusService->getAllstatus();
        $tasks = $this->taskService->getAlltask()->toArray();

        $tasksTemp = array();
        foreach ($tasks as $key => $item) {
            $tasksTemp[$item['task_status']][] = [
                "id" => encrypt($item['id']),
                "title" => $item['title'],
                "comments" => "0",
                "badge-text" => "UX",
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
        // $tasks = $this->taskService->getAlltask();
        $user = auth()->user();

        $tasks = $user->tasks()->where('status', '0');

        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            // dd($row->task_id);
            $encryptedId = encrypt($row->task_id);
            // Update Button
            // $updateButton = "<a class='btn btn-warning' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $acceptButton = "<a class='btn btn-success btn-sm me-1' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='check-circle'></i></a>";
            $rejectButton = "<a href='#' class='btn  btn-danger btn-sm me-1 reject-btn' data-id='$encryptedId' data-toggle='modal' data-target='#exampleModal'><i class='ficon' data-feather='x-circle'></i></a>";
            // $updateButton = "<a class='btn btn-warning  '  href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            // $acceptButton = "<a class='btn btn-warning  '  href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";

            $buttons = " " . $acceptButton . "  " . $rejectButton . " " . $viewbutton;
            return "<div class='d-flex justify-content-between'>" . $buttons . "</div>";
        })->addColumn('created_by_username', function ($row) {
            if ($row->creator) {
                return ($row->creator->first_name . " " . $row->creator->last_name) ?? '-';
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
            })->rawColumns(['actions'])->make(true);
    }

    public function reject_task(Request $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $taskDetails = Task::where('id', $id)->first();
            $createdById = $taskDetails->created_by;
            $departmentId = $taskDetails->department_id;
            $departmentDetails = Department::where('id', $departmentId)->first();
            $userDetails = User::where('id', $createdById)->first();
            $departmentHOD = User::where('id', $departmentDetails->hod)->first();
            $userId = auth()->user()->id;
            TaskAssignee::where('user_id', $userId)
                ->where('task_id', $id)
                ->update(['status' => 2, 'remark' => $request->get('remark')]);
            $userDetails = $userDetails->email;
            $hodMail = $departmentHOD->email;
            $subject = "Task Rejected";
            $html = View::make('emails.task_Rejected', compact('taskDetails'))->render();
            Mail::to($userDetails)->send(new TaskCreatedMail($subject, $html));
            Mail::to($hodMail)->send(new TaskCreatedMail($subject, $html));
            return redirect()->route("app-task-requested")->with('success', 'Task Rejected Successfully');
        } catch (\Exception $error) {

            return redirect()->route("app-task-requested")->with('error', 'Error While Rejecting Task');
        }
    }

    public function getAll_accepted()
    {
        // dd('getAll_accepted');
        // $tasks = $this->taskService->getAlltask();
        $user = auth()->user();

        $tasks = $user->tasks()->where('status', '1');

        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            // Update Button
            $updateButton = "<a class='btn btn-warning btn-sm me-1'  href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            // Delete Button
            $deleteButton = "<a class='btn btn-danger confirm-delete btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function accept_task($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $userId = auth()->user()->id;

            $task_ass = TaskAssignee::where('user_id', $userId)
                ->where('task_id', $id)
                ->update(['status' => 1]);
            // dd($task_ass);
            $task = Task::where('id', $id)->first();

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
        $projects = Project::where('status', 'on')->get();
        $departments = Department::where('status', 'on')->get();
        $Subdepartments = SubDepartment::where('status', 'on')->get();
        $Status = Status::where('status', 'on')->get();
        $Prioritys = Priority::where('status', 'on')->get();
        $users = User::where('status', '1')->get();
        $departmentslist = $this->taskService->getAlltask();
        $data['department'] = Task::all();
        // $data['parent'] = Task::with('parent')->whereNull('parent_id')->get();
        // $selectedparentDepartment = '';
        return view('.content.apps.task.create-edit', compact('page_data', 'task', 'departmentslist', 'data', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys'));
    }

    public function store(CreateTaskRequest $request)
    {
        try {

            $taskData['title'] = $request->get('title');
            $taskData['description'] = $request->get('description');
            $taskData['subject'] = $request->get('subject');
            $taskData['project_id'] = $request->get('project_id');
            $taskData['start_date'] = $request->get('start_date');
            $taskData['due_date'] = $request->get('due_date');
            $taskData['priority_id'] = $request->get('priority_id');
            $taskData['department_id'] = $request->get('department_id');
            $taskData['sub_department_id'] = $request->get('sub_department_id');
            $taskData['task_status'] = $request->get('task_status');
            $taskData['created_by'] = auth()->user()->id;
            $task = $this->taskService->create($taskData);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $filename = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments'); // Assuming 'attachments' is the folder inside the storage/app/public directory
                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'file' => $path,
                    ]);
                }
            }
            $userIds = $request->input('user_id', []);
            $task->users()->sync($userIds);
            $task->users()->updateExistingPivot($userIds, ['status' => 0]);
            $authUserId = auth()->user()->id;
            if (in_array($authUserId, $userIds)) {
                $task->users()->updateExistingPivot($authUserId, ['status' => 1]);
            }

            // dd($task->users);

            $loggedInUser = auth()->user();
            // dd($loggedInUser);
            $encryptedId = encrypt($task->id);
            $task->encryptedId = $encryptedId;
            // dd($task);

            // $acceptButton = "<a class='btn btn-success btn-sm me-1' href='" . route('app-task-accept', $encryptedId) . "'><i class='ficon' data-feather='thumbs-up'></i></a>";
            // $rejectButton = "<a href='#' class='btn  btn-danger btn-sm me-1 reject-btn' data-id='$encryptedId' data-toggle='modal' data-target='#exampleModal'><i class='ficon' data-feather='thumbs-down'></i></a>";


            $html = View::make('emails.task_created', compact('task'))->render();
            $subject = "New Task Created";
            // echo ($html);
            // die;
            foreach ($task->users as $user) {
                // dump($user->email);
                $mail = Mail::to($user->email)->send(new TaskCreatedMail($subject, $html));

            }
            // die;
            // $mail = Mail::to('pradip12345.pv@gmail.com')->send(new TaskCreatedMail($subject, $html));
            if (!empty($mail)) {
                return redirect()->route("app-task-list")->with('success', 'Task Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Task');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-task-list")->with('error', 'Error while adding Task');
        }
    }

    public function edit($encrypted_id)
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
            $users = User::where('status', '1')->get();
            $departmentslist = $this->taskService->getAlltask();
            $data['department'] = Task::all();
            $associatedSubDepartmentId = $task->subDepartment->id ?? null;

            return view('.content.apps.task.create-edit', compact('page_data', 'task', 'data', 'departmentslist', 'projects', 'users', 'departments', 'Subdepartments', 'Status', 'Prioritys', 'associatedSubDepartmentId'));
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
    public function update(UpdateTaskRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $taskData['title'] = $request->get('title');
            $taskData['description'] = $request->get('description');
            $taskData['subject'] = $request->get('subject');
            $taskData['project_id'] = $request->get('project_id');
            $taskData['start_date'] = $request->get('start_date');
            $taskData['due_date'] = $request->get('due_date');
            $taskData['priority_id'] = $request->get('priority_id');
            $taskData['department_id'] = $request->get('department_id');
            $taskData['sub_department_id'] = $request->get('sub_department_id');
            $taskData['task_status'] = $request->get('task_status');
            $taskData['updated_by'] = auth()->user()->id;

            $updated = $this->taskService->updatetask($id, $taskData);

            // Handle attachments update
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $filename = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments'); // Assuming 'attachments' is the folder inside the storage/app/public directory

                    TaskAttachment::create([
                        'task_id' => $id, // Use the existing task ID for attachments
                        'file' => $path,
                    ]);
                }
            }

            // Handle user assignment update
            $userIds = $request->input('user_id', []);
            $task = Task::find($id);
            $task->users()->sync($userIds);

            if (!empty($updated)) {
                return redirect()->route("app-task-list")->with('success', 'Task Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Task');
            }
        } catch (\Exception $error) {
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
        $users = User::where('department_id', $department_id)->get();
        return response()->json($users);
    }

    public function getAll_conceptualization()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 1)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
        $tasks = Task::where('due_date', '<', now()) // Select tasks with due date in the past
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter tasks assigned to authenticated user
            })
            ->with([
                'assignees' => function ($query) use ($userId) {
                    $query->where('user_id', $userId); // Load only assignees for authenticated user
                }
            ]);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_scope_defined()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 3)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_completed()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 4)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_in_execution()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 5)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);
    }

    public function getAll_hold()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', 6)
            ->whereHas('assignees', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 1);
            });
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger confirm-delete me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_admin_total()
    {
        $userId = auth()->user()->id;
        $tasks = Task::whereHas('assignees', function ($query) {
            $query->where('status', 1);
        });
        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);

    }

    public function getAll_total_task()
    {
        $userId = auth()->user()->id;
        $tasks = Task::where('task_status', '!=', 2)->get();
        // dd($tasks);
        return DataTables::of($tasks)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning me-1' href='" . route('app-task-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";
            $deleteButton = "<a class='btn btn-danger me-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";
            $viewbutton = "<a class='btn btn-info btn-sm me-1' data-idos='$encryptedId' id='confirm-color' href='" . route('app-task-view', $encryptedId) . "'><i class='ficon' data-feather='eye'></i></a>";
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
            })->rawColumns(['actions'])->make(true);

    }
}
