<?php

namespace App\Http\Controllers;

use App\Models\SoftwareProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DesktopAuthController extends Controller
{
    /**
     * Display the desktop SSO authorization or login screen.
     */
    public function prompt(Request $request)
    {
        $appSlug = strtolower(trim((string) $request->query('app', 'thundercall')));
        $port = (int) $request->query('port', 0);
        $state = (string) $request->query('state', '');
        $redirectUri = (string) $request->query('redirect_uri', '');

        // Resolve the target software project
        $project = SoftwareProject::where('slug', $appSlug)->first();
        if (!$project && $appSlug === 'thundercall') {
            $project = SoftwareProject::where('name', 'like', '%ThunderCall%')->first();
        }

        // Store SSO return parameters in session
        session(['desktop_sso' => [
            'app' => $appSlug,
            'port' => $port,
            'state' => $state,
            'redirect_uri' => $redirectUri,
        ]]);

        $user = Auth::user();

        return view('auth.desktop-sso', [
            'user' => $user,
            'project' => $project,
            'appSlug' => $appSlug,
            'port' => $port,
            'state' => $state,
            'redirectUri' => $redirectUri,
        ]);
    }

    /**
     * Handle the desktop SSO authorization action.
     */
    public function authorizeApp(Request $request)
    {
        $request->validate([
            'port' => 'nullable|integer',
            'state' => 'nullable|string',
            'app' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.desktop', $request->all());
        }

        $appSlug = strtolower(trim($request->input('app')));
        $port = (int) $request->input('port', 0);
        $state = (string) $request->input('state', '');
        $redirectUri = (string) $request->input('redirect_uri', '');

        // Generate an ecosystem token for this desktop session
        $token = 'rozehub_sso_' . Str::random(48);
        $refreshToken = 'rozehub_refresh_' . Str::random(48);

        // Record interaction if user_project_interactions exists
        try {
            $project = SoftwareProject::where('slug', $appSlug)->first();
            if ($project) {
                \App\Models\UserProjectInteraction::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'software_project_id' => $project->id,
                        'event_type' => 'desktop_sso_connect',
                    ],
                    [
                        'count' => \DB::raw('count + 1'),
                        'last_occurred_at' => now(),
                    ]
                );
            }
        } catch (\Throwable $ignored) {}

        // Construct the loopback callback URL if a valid port was provided
        $loopbackUrl = null;
        if ($port > 0) {
            $loopbackUrl = sprintf(
                'http://127.0.0.1:%d/callback?token=%s&refreshToken=%s&username=%s&email=%s&state=%s&app=%s&id=%d',
                $port,
                urlencode($token),
                urlencode($refreshToken),
                urlencode($user->name),
                urlencode($user->email),
                urlencode($state),
                urlencode($appSlug),
                $user->id
            );
        }

        // Custom protocol deep link (e.g. thundercall://auth/callback?...)
        $deepLinkUrl = sprintf(
            '%s://auth/callback?token=%s&refreshToken=%s&username=%s&email=%s&state=%s&app=%s&id=%d',
            preg_replace('/[^a-z0-9_-]/', '', $appSlug),
            urlencode($token),
            urlencode($refreshToken),
            urlencode($user->name),
            urlencode($user->email),
            urlencode($state),
            urlencode($appSlug),
            $user->id
        );

        $project = SoftwareProject::where('slug', $appSlug)->first();

        return view('auth.desktop-success', [
            'user' => $user,
            'project' => $project,
            'appSlug' => $appSlug,
            'token' => $token,
            'loopbackUrl' => $loopbackUrl,
            'deepLinkUrl' => $deepLinkUrl,
        ]);
    }

    /**
     * Central ecosystem credential verification endpoint.
     * Allows desktop applications (ThunderCall, DBNavigator, Lumina, etc.)
     * to authenticate users directly with their RozeHub account credentials.
     */
    public function verifyCredentials(Request $request)
    {
        $login = trim((string) $request->input('login', $request->input('username', '')));
        $password = (string) $request->input('password', '');

        if ($login === '' || $password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Login and password are required',
            ], 400);
        }

        // Find user by email or name (case-insensitive)
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($login)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($login)])
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Authenticated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_type' => $user->account_type ?? 'user',
            ],
        ]);
    }
}
