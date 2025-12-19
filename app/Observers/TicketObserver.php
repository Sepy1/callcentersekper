<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\DepWhatsappService;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Saat tiket dibuat → kirim WA OPEN
     */
    public function created(Ticket $ticket): void
    {
        if ($ticket->status !== 'open') {
            return;
        }

        try {
            app(DepWhatsappService::class)->sendTemplateById(
                phone: $this->normalizePhone($ticket->hp),
                templateId: '1557389545282102', // TEMPLATE OPEN
                language: 'id',
                params: [
                    $ticket->nama_pelapor,               // {{1}}
                    $ticket->nomor_tiket,                // {{2}}
                    $ticket->judul,                      // {{3}}
                    $ticket->kategori,                   // {{4}}
                    $ticket->created_at->format('d F Y') // {{5}}
                ]
            );

            Log::info('WA OPEN SENT', ['ticket_id' => $ticket->id]);
        } catch (\Throwable $e) {
            Log::error('WA OPEN FAILED', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Saat tiket di-close → kirim WA CLOSE
     */
    public function updated(Ticket $ticket): void
    {
        if (
            $ticket->isDirty('status') &&
            $ticket->status === 'closed'
        ) {
            try {
                app(DepWhatsappService::class)->sendTemplateById(
                    phone: $this->normalizePhone($ticket->hp),
                    templateId: '734693812466588', // TEMPLATE CLOSE
                    language: 'id',
                    params: [
                        $ticket->nama_pelapor,                    // {{1}}
                        $ticket->nomor_tiket,                     // {{2}}
                        optional($ticket->closing_at)
                            ->format('d F Y') ?? now()->format('d F Y'), // {{3}}
                        $ticket->closing_notes ?? '-'             // {{4}} ✅ FIX
                    ]
                );

                Log::info('WA CLOSE SENT', ['ticket_id' => $ticket->id]);
            } catch (\Throwable $e) {
                Log::error('WA CLOSE FAILED', [
                    'ticket_id' => $ticket->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Normalisasi nomor HP ke 62xxxxxxxx
     */
    private function normalizePhone(?string $phone): string
    {
        if (!$phone) return '';

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '08')) {
            return '628' . substr($phone, 2);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
