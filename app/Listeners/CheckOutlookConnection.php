<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\OutlookService;

class CheckOutlookConnection
{
    protected $outlook;

    public function __construct(OutlookService $outlook)
    {
        $this->outlook = $outlook;
    }

    public function handle(Login $event)
    {
        $user = $event->user;

        // Only try if there's already a refresh token saved
        if ($user->outlook_refresh_token) {
            $this->outlook->getAccessToken($user); // Will refresh token if needed
        }
    }
}
