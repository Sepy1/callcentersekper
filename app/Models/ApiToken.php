<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $table = 'api_tokens';

    protected $fillable = ['name', 'token', 'abilities', 'allowed_ips'];

    protected $casts = [
        'abilities' => 'array',
        'allowed_ips' => 'array',
    ];
}
