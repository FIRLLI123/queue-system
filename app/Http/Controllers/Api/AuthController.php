<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'status' => 'ACTIVE',
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        $token = $request->user()->createToken('api-token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'description' => sprintf('%s logged into the system.', $user->username),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $queueService = app(\App\Services\QueueService::class);

            // Move to last queue position immediately
            $queueService->moveUserToQueueEnd($user);

            // Clear last_seen_at so isOnline() returns false on next poll
            $user->update(['last_seen_at' => null]);

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'LOGOUT',
                'description' => sprintf('%s logged out of the system.', $user->username),
            ]);
        }

        $token = $request->user()->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'The current password is incorrect.'], 422);
        }

        $user->update([
            'password' => $request->input('new_password'),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CHANGE_PASSWORD',
            'description' => sprintf('%s changed their password.', $user->username),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
