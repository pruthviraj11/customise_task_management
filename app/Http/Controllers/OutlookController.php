<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class OutlookController extends Controller
{
    public function redirectToMicrosoft()
    {
        $user = Auth::user();

        $query = http_build_query([
            'client_id' => $user->outlook_client_id,
            'response_type' => 'code',
            'redirect_uri' => $user->outlook_redirect_url,
            // 'redirect_uri' => 'http://localhost:8000/app/task/list',

            'response_mode' => 'query',
            'scope' => 'offline_access Calendars.ReadWrite',
            'state' => '12345',
        ]);
        return redirect("https://login.microsoftonline.com/{$user->outlook_tenant_id}/oauth2/v2.0/authorize?$query");
    }

    public function handleMicrosoftCallback(Request $request)
    {

        $user = Auth::user();
        $response = Http::asForm()->post("https://login.microsoftonline.com/{$user->outlook_tenant_id}/oauth2/v2.0/token", [
            'client_id' => $user->outlook_client_id,
            // 'client_secret' => $user->outlook_client_secret,
            'code' => $request->code,
            'redirect_uri' => $user->outlook_redirect_url,
            'grant_type' => 'authorization_code',
        ]);
        // dd($response->status(), $response->body(), $response->ok());
        if ($response->ok()) {
            $data = $response->json();
            // Save tokens
            $user->update([
                'outlook_access_token' => $data['access_token'] ?? null,
                'outlook_refresh_token' => $data['refresh_token'] ?? null,
                'outlook_token_expires' => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);
            // return redirect('/app/task/list')->with('success', 'Outlook connected successfully!');
            return redirect()->route('app-task-list')->with('success', 'Outlook connected successfully!');
        } else {
            // Optional: log error details
            \Log::error('Outlook OAuth Token Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return redirect()->route('app-task-list')->with('error', 'Outlook connection failed. Please try again.');
        }
    }
}
