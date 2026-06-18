<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class WebChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        // Auto Cleanup: Hapus pesan yang umurnya lebih dari 24 jam (1 hari)
        Chat::where('created_at', '<', now()->subDay())->delete();

        return response()->json([
            'success' => true,
            'chat' => $chat->load('sender')
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);

        Chat::where('receiver_id', $request->user()->id)
            ->where('sender_id', $request->input('sender_id'))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
