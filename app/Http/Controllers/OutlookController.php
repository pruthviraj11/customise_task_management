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
        $company = strtoupper(str_replace(' ', '_', $user->company)); // e.g., "Green Panther" → "GREEN_PANTHER"

        $clientId = env("OUTLOOK_CLIENT_ID_{$company}");
        $redirectUri = env("OUTLOOK_REDIRECT_URL");
        $tenantId = env("OUTLOOK_TENANT_ID_{$company}");
        $query = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            // 'redirect_uri' => 'http://localhost:8000/app/task/list',

            'response_mode' => 'query',
            'scope' => 'offline_access Calendars.ReadWrite',
            'state' => '12345',
        ]);

        return redirect("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?$query");
    }

    // public function handleMicrosoftCallback(Request $request)
    // {

    //     $user = Auth::user();
    //     $response = Http::asForm()->post("https://login.microsoftonline.com/{$user->outlook_tenant_id}/oauth2/v2.0/token", [
    //         'client_id' => $user->outlook_client_id,
    //         // 'client_secret' => $user->outlook_client_secret,
    //         'code' => $request->code,
    //         'redirect_uri' => $user->outlook_redirect_url,
    //         'grant_type' => 'authorization_code',
    //     ]);
    //     // dd($response->status(), $response->body(), $response->ok());
    //     if ($response->ok()) {
    //         $data = $response->json();
    //         // Save tokens
    //         $user->update([
    //             'outlook_access_token' => $data['access_token'] ?? null,
    //             'outlook_refresh_token' => $data['refresh_token'] ?? null,
    //             'outlook_token_expires' => now()->addSeconds($data['expires_in'] ?? 3600),
    //         ]);
    //         // return redirect('/app/task/list')->with('success', 'Outlook connected successfully!');
    //         return redirect()->route('app-task-list')->with('success', 'Outlook connected successfully!');
    //     } else {
    //         // Optional: log error details
    //         \Log::error('Outlook OAuth Token Error', [
    //             'status' => $response->status(),
    //             'body' => $response->body()
    //         ]);

    //         return redirect()->route('app-task-list')->with('error', 'Outlook connection failed. Please try again.');
    //     }
    // }

    public function handleMicrosoftCallback(Request $request)
    {
        $user = Auth::user();

        // Get company-based credentials
        $company = strtoupper(str_replace(' ', '_', $user->company));

        $clientId = env("OUTLOOK_CLIENT_ID_{$company}");
        $clientSecret = env("OUTLOOK_CLIENT_SECRET_{$company}");
        $tenantId = env("OUTLOOK_TENANT_ID_{$company}");
        $redirectUri = env("OUTLOOK_REDIRECT_URL");

        // Optional: validate
        if (!$clientId || !$clientSecret || !$tenantId || !$redirectUri) {
            abort(500, "Missing Outlook credentials for company: {$user->company}");
        }

        // Request access token
        $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            // 'client_secret' => $clientSecret,
            'code' => $request->code,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if ($response->ok()) {
            $data = $response->json();

            // Save tokens to user
            $user->update([
                'outlook_access_token' => $data['access_token'] ?? null,
                'outlook_refresh_token' => $data['refresh_token'] ?? null,
                'outlook_token_expires' => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);

            return redirect()->route('app-task-list')->with('success', 'Outlook connected successfully!');
        } else {
            \Log::error('Outlook OAuth Token Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return redirect()->route('app-task-list')->with('error', 'Outlook connection failed. Please try again.');
        }
    }

}
