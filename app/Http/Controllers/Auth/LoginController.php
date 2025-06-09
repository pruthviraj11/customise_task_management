<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // Check if the logged-in user's ID is 1
        if (Auth::user()->id == 1) {
            return '/app/task/list'; // Change this path for user ID 1
        }

        $user = Auth::user();

        // Log activity
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'notification_to' => $user->id,
                'message' => 'User logged in',
                'notification_type' => 'login',
                'notification_status' => 'unread',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ])
            ->log('User logged in');
        return '/app/task/mytask'; // Default redirect path
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        $user = Auth::user();
        if ($user) {
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'notification_to' => $user->id,
                    'message' => 'User logged out',
                    'notification_type' => 'logout',
                    'notification_status' => 'unread',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ])
                ->log('User logged out');
        }
        return redirect()->route('login');
    }
}
