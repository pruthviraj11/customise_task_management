<?php
namespace App\Services;

use Microsoft\Graph\Graph;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OutlookService
{
    // public function getAccessToken($user)
    // {
    //     if ($user->outlook_token_expires && now()->lt($user->outlook_token_expires)) {
    //         return $user->outlook_access_token;
    //     }

    //     $response = Http::asForm()->post("https://login.microsoftonline.com/{$user->outlook_tenant_id}/oauth2/v2.0/token", [
    //         'client_id' => $user->outlook_client_id,
    //         'client_secret' => $user->outlook_client_secret,
    //         'refresh_token' => $user->outlook_refresh_token,
    //         'redirect_uri' => $user->outlook_redirect_url,
    //         'grant_type' => 'refresh_token',
    //     ]);

    //     if ($response->ok()) {
    //         $data = $response->json();

    //         $user->update([
    //             'outlook_access_token' => $data['access_token'],
    //             'outlook_refresh_token' => $data['refresh_token'] ?? $user->outlook_refresh_token,
    //             'outlook_token_expires' => now()->addSeconds($data['expires_in']),
    //         ]);

    //         return $data['access_token'];
    //     }

    //     return null;
    // }

    public function getAccessToken($user)
    {
        // If token is still valid, return it
        if ($user->outlook_token_expires && now()->lt($user->outlook_token_expires)) {
            return $user->outlook_access_token;
        }

        // Prepare company-based credentials
        $company = strtoupper(str_replace(' ', '_', $user->company));

        $clientId = env("OUTLOOK_CLIENT_ID_{$company}");
        $clientSecret = env("OUTLOOK_CLIENT_SECRET_{$company}");
        $tenantId = env("OUTLOOK_TENANT_ID_{$company}");
        $redirectUri = env("OUTLOOK_REDIRECT_URL");

        if (!$clientId || !$clientSecret || !$tenantId || !$redirectUri) {
            \Log::error("Missing Outlook credentials for company: {$user->company}");
            return null;
        }

        // Request new access token using refresh token
        $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $user->outlook_refresh_token,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->ok()) {
            $data = $response->json();

            $user->update([
                'outlook_access_token' => $data['access_token'],
                'outlook_refresh_token' => $data['refresh_token'] ?? $user->outlook_refresh_token,
                'outlook_token_expires' => now()->addSeconds($data['expires_in']),
            ]);

            return $data['access_token'];
        }

        \Log::error("Failed to refresh Outlook token", [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }


  public function createEvent($user, $task)
{
    $token = $this->getAccessToken($user);
    if (!$token) return null;

    $graph = new Graph();
    $graph->setAccessToken($token);

    $event = [
        'subject' => $task['title'] . ' (' . $task['id'] . ')',
        'body' => [
            'contentType' => 'HTML',
            'content' => ($task['description'] ?? '') . '<br><br><a href="' . url('/app/task/view/' . encrypt($task['id'])) . '">View Task in System</a>',
        ],
        'start' => [
            'dateTime' => \Carbon\Carbon::parse($task['start_date'] . ' 10:00:00')->format('Y-m-d\TH:i:s'),
            'timeZone' => 'Asia/Kolkata',
        ],
        'end' => [
            'dateTime' => \Carbon\Carbon::parse($task['due_date'] . ' 19:00:00')->format('Y-m-d\TH:i:s'),
            'timeZone' => 'Asia/Kolkata',
        ],
    ];

    $response = $graph->createRequest('POST', '/me/events')
        ->attachBody($event)
        ->execute();

    // Convert GraphResponse to array
    return json_decode($response->getBody(), true);
}


    // public function createEvent($user, $task)
    // {
    //     $token = $this->getAccessToken($user);
    //     if (!$token)
    //         return null;

    //     $graph = new Graph();
    //     $graph->setAccessToken($token);

    //     $event = [
    //         'subject' => $task['title'] . ' (' . $task['id'] . ')',
    //         'body' => [
    //             'contentType' => 'HTML',
    //             'content' => ($task['description'] ?? '') . '<br><br><a href="' . url('/app/task/view/' . encrypt($task['id'])) . '">View Task in System</a>',
    //         ],
    //         'start' => [
    //             'dateTime' => Carbon::parse($task['start_date'] . ' 10:00:00')->format('Y-m-d\TH:i:s'),
    //             'timeZone' => 'Asia/Kolkata',
    //         ],
    //         'end' => [
    //             'dateTime' => Carbon::parse($task['due_date'] . ' 19:00:00')->format('Y-m-d\TH:i:s'),
    //             'timeZone' => 'Asia/Kolkata',
    //         ],
    //     ];

    //     return $graph->createRequest('POST', '/me/events')
    //         ->attachBody($event)
    //         ->execute();
    // }

    public function updateEvent($user, $task)
    {
        $token = $this->getAccessToken($user);
        if (!$token)
            return null;


        $graph = new Graph();
        $graph->setAccessToken($token);

        $event = [
            'subject' => $task['title'] . ' (' . $task['id'] . ')',
            'body' => [
                'contentType' => 'HTML',
                'content' => ($task['description'] ?? '') . '<br><br><a href="' . url('/app/task/view/' . encrypt($task['id'])) . '">View Task in System</a>',
            ],
            'start' => [
                'dateTime' => Carbon::parse($task['start_date'] . ' 10:00:00')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => Carbon::parse($task['due_date'] . ' 19:00:00')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Kolkata',
            ],
        ];

        try {
            return $graph->createRequest('PATCH', '/me/events/' . $task['outlook_event_id'])
                ->attachBody($event)
                ->execute();
        } catch (\Exception $e) {
            \Log::error("Failed to update Outlook event for task ID {$task['id']}: " . $e->getMessage());
            return null;
        }
    }

}
