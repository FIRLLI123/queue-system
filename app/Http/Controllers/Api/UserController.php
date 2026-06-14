<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function index()
    {
        $users = User::with('queuePosition')->orderBy('name')->get();
        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['ADMIN', 'CC'])],
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => $request->input('password'), // setPasswordAttribute mutator will hash it
                'role' => $request->input('role'),
                'status' => $request->input('status'),
            ]);

            $this->queueService->syncQueueForUser($user);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'CREATE_USER',
                'description' => sprintf('Admin created user %s (%s).', $user->username, $user->role),
            ]);

            return $user;
        });

        return response()->json(['user' => $user->load('queuePosition')], 201);
    }

    public function show($id)
    {
        $user = User::with('queuePosition')->findOrFail($id);
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['sometimes', 'required', Rule::in(['ADMIN', 'CC'])],
            'status' => ['sometimes', 'required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        $updatedUser = DB::transaction(function () use ($request, $user) {
            $data = $request->only(['name', 'username', 'email', 'role', 'status']);
            
            if ($request->filled('password')) {
                $data['password'] = $request->input('password');
            }

            $user->update($data);

            $this->queueService->syncQueueForUser($user);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'UPDATE_USER',
                'description' => sprintf('Admin updated user %s.', $user->username),
            ]);

            return $user;
        });

        return response()->json(['user' => $updatedUser->load('queuePosition')]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 400);
        }

        DB::transaction(function () use ($request, $user) {
            $user->delete();
            
            $this->queueService->syncQueueForUser($user, true); // true for isDeleted

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'DELETE_USER',
                'description' => sprintf('Admin deleted user %s.', $user->username),
            ]);
        });

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
