<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleTicket extends Model
{
    protected $table = 'recycle_tickets';

    protected $fillable = [
        'original_ticket_id',
        'data',
        'deleted_by',
        'deleted_ip',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
