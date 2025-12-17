<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function messages($nomor_tiket)
    {
        $ticket = Ticket::where('nomor_tiket', $nomor_tiket)->firstOrFail();
        $msgs = ChatMessage::with('user')->where('ticket_id', $ticket->id)->orderBy('created_at')->get();
        return response()->json($msgs);
    }

    public function send(Request $request)
    {
        // only accept genuine chat sends from the chat widget (require header)
        if (!$request->header('X-CHAT-SEND')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'nomor_tiket' => 'required|string',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240' // max 10MB
        ]);

        $ticket = Ticket::where('nomor_tiket', $request->input('nomor_tiket'))->firstOrFail();
        $user = Auth::user();

        $data = [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = Str::random(12) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('chat', $filename, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_type'] = $file->getClientMimeType();
        }

        $msg = ChatMessage::create($data);

        $msg->load('user');

        return response()->json($msg, 201);
    }
}
