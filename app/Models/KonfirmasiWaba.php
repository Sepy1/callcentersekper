<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfirmasiWaba extends Model
{
    use HasFactory;

    protected $table = 'konfirmasi_waba';

    protected $fillable = [
        'nasabah_id',
        'nama_nasabah',
        'alamat',
        'hp',
        'waba',
    ];
}
