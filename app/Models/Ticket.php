<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'nomor_tiket', 'nama_pelapor', 'hp', 'email', 'kategori', 'judul', 'detail', 'tindak_lanjut', 'qa_summary', 'status', 'officer', 'waktu_eskalasi', 'tipe_pelapor', 'is_nasabah', 'id_ktp', 'nomor_rekening', 'nama_ibu', 'alamat', 'tempat_lahir', 'tgl_lahir', 'kode_kantor', 'upload_ktp', 'upload_bukti', 'media_closing', 'closing_at'
    ];

    protected $casts = [
        'closing_at' => 'datetime',
    ];

    public function officers()
    {
        return $this->belongsToMany(User::class, 'ticket_officer', 'ticket_id', 'officer_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
