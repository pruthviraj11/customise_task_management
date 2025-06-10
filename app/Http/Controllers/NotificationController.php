<?php

namespace App\Http\Controllers;

use App\Models\InternalNotifications;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function index()
    {
        $type = last(explode('-', request()->route()->getName()));
        return view('content/apps/notification/list', compact('type'));
    }

    public function getAll(Request $request)
    {
        $internalNotifications = InternalNotifications::where('notification_to', auth()->user()->id)
            ->leftJoin('users', 'internal_notifications.notification_from', '=', 'users.id')
            ->select('internal_notifications.*', 'users.first_name','users.last_name');
        if ($request->get('notification_type') == 'unread') {
            $internalNotifications->where('internal_notifications.notification_status', false);
        } elseif ($request->get('notification_type') == 'read') {
            $internalNotifications->where('internal_notifications.notification_status', true);
        }


        // ✅ Custom global search
        if (!empty($request->search['value'])) {
            $searchTerm = $request->search['value'];
            $internalNotifications->where(function ($query) use ($searchTerm) {
                $query->where('internal_notifications.message', 'like', '%' . $searchTerm . '%')
                    ->orWhere('internal_notifications.notification_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("DATE_FORMAT(internal_notifications.created_at, '%d-%m-%Y') like ?", ["%$searchTerm%"])
                    ->orWhereRaw("DATE_FORMAT(internal_notifications.created_at, '%d-%m-%Y %h:%i %p') like ?", ["%$searchTerm%"]);
            });
        }
        // $internalNotifications->get();
        return \Yajra\DataTables\Facades\DataTables::of($internalNotifications)
        ->addColumn('full_name', function ($user) {
        return $user->first_name . ' ' . $user->last_name;
    })
            ->addColumn('actions', function ($row) {
                if ($row->notification_status == false) {
                    $encryptedId = encrypt($row->id);
                    $viewDetails = "<button data-bs-toggle='tooltip' id='mark-as-read' title='View Details' data-bs-delay='400' class='btn btn-sm btn-primary mt-1 view_detail' data-internal-notification-id = '" . $encryptedId . "'  href=''><i class='ficon' data-feather='check-circle'></i> Read</button>";

                } else {
                    $viewDetails = "";
                }

                return $viewDetails;

            })

            ->addColumn('notification_date', function ($row) {
                return date('d-m-Y h:i A', strtotime($row->created_at));
            })
            ->rawColumns(['actions', 'full_name','message', 'notification_date'])
            ->filter(function ($query) {
                // This disables the default search behavior
            })
            ->make(true);
    }

    public function getAllRecentNotification()
    {
        return response()->json(getNotifications());
    }

    public function notification_read($id = '')
    {
        try {
            $notification = InternalNotifications::query();
            if ($id) {
                $notification->where('id', decrypt($id))->update(['notification_status' => true]);
                return response()->json(['status' => true, 'message' => 'Notifications marked as Read...!']);
            } else {
                $notification->where('notification_to', auth()->user()->id)
                    ->where('notification_status', false)
                    ->update(['notification_status' => true]);
                return response()->json(['status' => true, 'message' => 'All notifications marked as Read...!']);
            }

        } catch (\Exception $error) {
            return response()->json(['status' => true, 'message' => 'invalid route found, no action performed', 'error' => $error->getMessage()]);
        }
    }

    public function notification_mark_read($id = '')
    {
        try {
            $notification = InternalNotifications::query();
            if ($id) {
                $notification->where('id', $id)->update(['notification_status' => true]);
                return redirect()->back()->with('success', 'Marked Read Successfully...!');
            } else {
                return redirect()->back()->with('error', 'Something went wrong...!');
            }

        } catch (\Exception $error) {
            return response()->json(['status' => true, 'message' => 'invalid route found, no action performed', 'error' => $error->getMessage()]);
        }
    }
}
