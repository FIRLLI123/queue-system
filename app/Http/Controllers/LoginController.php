<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'status'   => 'ACTIVE',
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'LOGIN',
                'description' => sprintf('%s logged into the web system.', $user->username),
            ]);

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Resolve QueueService from the container
            $queueService = app(\App\Services\QueueService::class);

            // 1. Move user to the last queue position immediately so others see the
            //    correct order on the very next poll (before last_seen_at expires).
            $queueService->moveUserToQueueEnd($user);

            // 2. Clear last_seen_at so isOnline() returns false instantly —
            //    no waiting for the 90-second threshold to expire.
            $user->update(['last_seen_at' => null]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action'  => 'LOGOUT',
                'description' => sprintf('%s logged out of the web system.', $user->username),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The current password is incorrect.'], 422);
            }
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => $request->input('new_password'),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CHANGE_PASSWORD',
            'description' => sprintf('%s changed their password.', $user->username),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Password updated successfully.']);
        }

        return back()->with('success', 'Password berhasil diubah!');
    }
}
