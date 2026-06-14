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

        $loginInput = $request->input('username');
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $loginInput,
            'password' => $request->input('password'),
            'status' => 'ACTIVE',
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'LOGIN',
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
