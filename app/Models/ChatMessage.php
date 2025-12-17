<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $fillable = ['ticket_id', 'user_id', 'message', 'attachment_path', 'attachment_type'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function ticket()
    {
        return $this->belongsTo(\App\Models\Ticket::class, 'ticket_id');
    }
}
