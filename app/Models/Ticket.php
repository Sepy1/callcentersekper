<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Notification;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'nomor_tiket', 'nama_pelapor', 'hp', 'email', 'kategori', 'judul', 'detail', 'tindak_lanjut', 'qa_summary', 'qa_attachment', 'status', 'officer', 'waktu_eskalasi', 'tipe_pelapor', 'is_nasabah', 'id_ktp', 'nomor_rekening', 'nama_ibu', 'alamat', 'tempat_lahir', 'tgl_lahir', 'kode_kantor', 'upload_ktp', 'upload_bukti', 'media_closing', 'closing_at'
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

    /**
     * Jika semua officer untuk ticket_id berstatus 'proses_qa',
     * kirim notifikasi "Tiket Perlu Di Resolved" ke semua QA.
     */
    public static function notifyQaIfAllOfficersProsesQa(int $ticketId)
    {
        try {
            $total = DB::table('ticket_officer')->where('ticket_id', $ticketId)->count();
            if ($total === 0) return;

            $prosesCount = DB::table('ticket_officer')
                ->where('ticket_id', $ticketId)
                ->where('status', 'proses_qa')
                ->count();

            if ($prosesCount !== $total) return;

            $ticket = self::find($ticketId);
            if (! $ticket) return;

            $title = 'Tiket Perlu Di Resolved';
            $message = "Tiket {$ticket->nomor_tiket} perlu di-resolved oleh QA.";
            $link = url('qa/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket);

            // ambil seluruh data user QA (email diperlukan untuk channel mail)
            $qas = User::where('role', 'qa')->get();
            foreach ($qas as $qa) {
                // hindari duplikat notifikasi untuk QA yang sama + ticket
                $exists = DB::table((new Notification)->getTable())
                    ->where('user_id', $qa->id)
                    ->where('title', $title)
                    ->whereRaw("JSON_EXTRACT(`data`, '$.ticket_id') = ?", [$ticketId])
                    ->exists();

                if (! $exists) {
                    Notification::create([
                        'user_id' => $qa->id,
                        'title' => $title,
                        'message' => $message,
                        'link' => $link,
                        'is_read' => false,
                        'data' => ['ticket_id' => $ticketId, 'nomor_tiket' => $ticket->nomor_tiket ?? null, 'reason' => 'all_officers_proses_qa'],
                    ]);
                }
            }

            // kirim email ke QA (jika ada) agar mereka diberitahu
            try {
                $notifiableQas = $qas->filter(fn($u) => !empty($u->email));
                if ($notifiableQas->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($notifiableQas, new \App\Notifications\TicketNeedQaNotification($ticket));
                }
            } catch (\Throwable $e) {
                Log::error('send QA needed email failed', ['ticket_id' => $ticketId, 'err' => $e->getMessage()]);
            }
        } catch (\Throwable $e) {
            Log::error('notifyQaIfAllOfficersProsesQa error', ['ticket_id' => $ticketId, 'err' => $e->getMessage()]);
        }
    }
}
