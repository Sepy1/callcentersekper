<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\Carbon;

class HelpdeskWhatsappNotifier
{
    /**
     * 🔔 NOTIFIKASI TIKET OPEN
     */
    public function notifyTicketOpen(Ticket $ticket): array
    {
        return app(DepWhatsappService::class)->sendTemplateById(
            $this->formatPhone($ticket->hp),
            '1557389545282102', // TEMPLATE ID OPEN
            'id',
            [
                $ticket->nama_pelapor,                    // {{1}}
                $ticket->nomor_tiket,                     // {{2}}
                $ticket->judul ?? '-',                    // {{3}}
                $ticket->kategori_nama,                   // {{4}}
                Carbon::parse($ticket->created_at)
                    ->translatedFormat('d F Y'),           // {{5}}
            ]
        );
    }

    /**
     * 🔔 NOTIFIKASI TIKET CLOSE
     */
    public function notifyTicketClose(Ticket $ticket): array
    {
        return app(DepWhatsappService::class)->sendTemplateById(
            $this->formatPhone($ticket->hp),
            '734693812466588', // TEMPLATE ID CLOSE
            'id',
            [
                $ticket->nama_pelapor,                    // {{1}}
                $ticket->nomor_tiket,                     // {{2}}
                Carbon::parse($ticket->closing_at ?? now())
                    ->translatedFormat('d F Y'),           // {{3}}
                $ticket->tindak_lanjut ?? '-',             // {{4}}
            ]
        );
    }

    /**
     * 📞 FORMAT NO HP KE 62
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
