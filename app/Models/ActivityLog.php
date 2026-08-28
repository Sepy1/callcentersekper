<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'action',
        'detail',
        'ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getActionLabelAttribute(): string
    {
        return [
            'ticket_created' => 'Tiket dibuat',
            'ticket_created_by_cabang' => 'Tiket dibuat oleh cabang',
            'officer_assigned' => 'Officer ditugaskan',
            'officer_unassigned' => 'Penugasan officer dibatalkan',
            'ticket_assigned_officers_updated' => 'Daftar officer diperbarui',
            'ticket_updated' => 'Data tiket diperbarui',
            'status_changed' => 'Status tiket diperbarui',
            'status_changed_by_qa' => 'Status diperbarui oleh QA',
            'qa_summary_updated' => 'Ringkasan QA diperbarui',
            'officer_tindak_lanjut' => 'Tindak lanjut officer disimpan',
            'officer_status_changed' => 'Status officer diperbarui',
            'ticket_deleted' => 'Tiket dihapus',
        ][$this->action] ?? ucfirst(str_replace('_', ' ', (string) $this->action));
    }

    public function getDetailIndonesiaAttribute(): string
    {
        $detail = trim((string) $this->detail);

        if (in_array($this->action, ['officer_assigned', 'officer_unassigned'], true)
            && preg_match('/(\d+)/', $detail, $matches)) {
            $officer = User::find($matches[1]);
            $name = $officer->name ?? ('ID ' . $matches[1]);

            return $this->action === 'officer_assigned'
                ? "Officer ditugaskan: {$name}"
                : "Penugasan officer dibatalkan: {$name}";
        }

        $replacements = [
            'Ticket created with nomor_tiket=' => 'Tiket dibuat dengan nomor ',
            'Ticket created by branch ' => 'Tiket dibuat oleh cabang ',
            'Officer list updated: ' => 'Daftar officer diperbarui: ',
            'QA updated summary: ' => 'QA memperbarui ringkasan: ',
            'Officer wrote tindak_lanjut' => 'Officer menulis tindak lanjut',
            'Officer status: ' => 'Status officer: ',
            'Updated ticket fields; before: ' => 'Data tiket diperbarui. Data sebelumnya: ',
            'Ticket moved to recycle bin' => 'Tiket dipindahkan ke tempat sampah',
            'closing_notes:' => 'catatan penutupan:',
        ];

        $detail = str_replace(array_keys($replacements), array_values($replacements), $detail);
        $detail = preg_replace_callback('/\b(open|in_progress|on_progress|proses_qa|cancel_qa|resolved|closed|rejected|assigned)\b/i', function ($matches) {
            return [
                'open' => 'Baru',
                'in_progress' => 'Sedang Diproses',
                'on_progress' => 'Sedang Diproses',
                'proses_qa' => 'Proses QA',
                'cancel_qa' => 'Dikembalikan QA',
                'resolved' => 'Selesai oleh QA',
                'closed' => 'Ditutup',
                'rejected' => 'Ditolak',
                'assigned' => 'Ditugaskan',
            ][strtolower($matches[1])] ?? $matches[1];
        }, $detail);

        $actor = $this->relationLoaded('user') ? $this->user : $this->user()->first();
        if ($actor && ! str_starts_with($detail, $actor->name)) {
            $detail = $actor->name . ($detail !== '' ? ' — ' . $detail : '');
        }

        return $detail !== '' ? $detail : '-';
    }
}
