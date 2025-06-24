<?php
namespace App\Services;

use Microsoft\Graph\Graph;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OutlookService
{
    public function getAccessToken($user)
    {
        if ($user->outlook_token_expires && now()->lt($user->outlook_token_expires)) {
            return $user->outlook_access_token;
        }

        $response = Http::asForm()->post("https://login.microsoftonline.com/{$user->outlook_tenant_id}/oauth2/v2.0/token", [
            'client_id' => $user->outlook_client_id,
            'client_secret' => $user->outlook_client_secret,
            'refresh_token' => $user->outlook_refresh_token,
            'redirect_uri' => $user->outlook_redirect_url,
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

        return null;
    }

    public function createEvent($user, $task)
    {
        // dd($user,$task,'Hii');
        $token = $this->getAccessToken($user);
        if (!$token) return null;

        // dd($task);
        $graph = new Graph();
        $graph->setAccessToken($token);

        $event = [
            'subject' => $task['title'],
           'body' => [
        'contentType' => 'HTML',
        'content' => ($task['description'] ?? '') . '<br><br><a href="' . url('/app/task/view/' .encrypt( $task['id'])) . '">View Task in System</a>',
    ],
            'start' => [
                'dateTime' => Carbon::parse($task['start_date'] . ' 10:00:00')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' =>Carbon::parse($task['due_date'] . ' 19:00:00')->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Asia/Kolkata',
            ],
        ];


        // dd($event);
        return $graph->createRequest('POST', '/me/events')
            ->attachBody($event)
            ->execute();
    }
}
