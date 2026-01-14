<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    use HasFactory;

    protected $table = 'api_request_logs';

    protected $fillable = [
        'api_token_id', 'method', 'path', 'headers', 'request_body', 'ip', 'response_status', 'response_body'
    ];

    protected $casts = [
        'headers' => 'array',
    ];
}
