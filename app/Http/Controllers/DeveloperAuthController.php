<?php

namespace App\Http\Controllers;

use App\Models\PublisherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeveloperAuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('developer.dashboard');
        }
        return view('developer.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The developer account credentials are incorrect.'])->onlyInput('email');
        }

        $user = Auth::user();

        // If a normal user signs in to the Developer Center, upgrade them with publisher capabilities
        if (!$user->is_developer && !$user->is_admin) {
            $user->update([
                'is_developer' => true,
                'account_type' => 'developer',
            ]);
            PublisherProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $user->name,
                    'slug' => Str::slug($user->name) . '-' . $user->id,
                    'is_verified' => false,
                ]
            );
        }

        $request->session()->regenerate();
        return redirect()->intended(route('developer.dashboard'));
    }

    public function registerForm()
    {
        if (Auth::check()) {
            return redirect()->route('developer.dashboard');
        }
        return view('developer.auth.register');
    }

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
            'is_developer' => true,
            'account_type' => 'developer',
            'email_verified_at' => now(),
        ]);

        PublisherProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . $user->id,
                'is_verified' => false,
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('developer.dashboard')->with('success', 'Developer publisher account created successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('developer.login');
    }
}
