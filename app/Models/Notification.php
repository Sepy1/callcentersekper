<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // jika Anda memakai tabel custom (sesuai migration sebelumnya)
    protected $table = 'notifications_custom';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'link',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
