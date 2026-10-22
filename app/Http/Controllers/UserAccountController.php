<?php

namespace App\Http\Controllers;

use App\Models\SoftwareProject;
use App\Models\User;
use App\Models\UserProjectInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserAccountController extends Controller
{
    /**
     * Show general user login form.
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            if ($request->session()->has('desktop_sso')) {
                return redirect()->route('auth.desktop', $request->session()->get('desktop_sso'));
            }
            return redirect()->route('account');
        }

        return view('auth.login');
    }

    /**
     * Process general user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid email address or password.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->session()->has('desktop_sso')) {
            $ssoParams = $request->session()->pull('desktop_sso');
            return redirect()->route('auth.desktop', $ssoParams);
        }

        return redirect()->intended(route('account'));
    }

    /**
     * Show general user registration form.
     */
    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('account');
        }

        return view('auth.register');
    }

    /**
     * Process user registration.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
            'is_developer' => false,
            'account_type' => 'user',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->session()->has('desktop_sso')) {
            $ssoParams = $request->session()->pull('desktop_sso');
            return redirect()->route('auth.desktop', $ssoParams);
        }

        return redirect()->route('account')->with('status', 'Your single RozeHub account has been created successfully!');
    }

    /**
     * Show the user profile and connected desktop applications dashboard.
     */
    public function account(Request $request)
    {
        $user = Auth::user();
        $projects = SoftwareProject::orderBy('name')->get();

        // Get recent interactions / connections
        $interactions = UserProjectInteraction::where('user_id', $user->id)
            ->get()
            ->keyBy('software_project_id');

        return view('account.index', [
            'user' => $user,
            'projects' => $projects,
            'interactions' => $interactions,
        ]);
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($data);

        return back()->with('status', 'Profile details updated.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Password changed successfully.');
    }

    /**
     * Log out current user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('hub');
    }
}
