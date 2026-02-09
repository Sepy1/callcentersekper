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
            // dispatch a queued job to send WA open template
            \App\Jobs\SendWhatsappTemplate::dispatch(
                $this->normalizePhone($ticket->hp),
                'notifikasi_tiket_open',
                'id',
                [
                    $ticket->nama_pelapor,
                    $ticket->nomor_tiket,
                    $ticket->judul,
                    $ticket->kategori,
                    $ticket->created_at->format('d F Y'),
                ]
            );

            Log::info('WA OPEN QUEUED', ['ticket_id' => $ticket->id]);
        } catch (\Throwable $e) {
            Log::error('WA OPEN QUEUE FAILED', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }

        // Schedule SLA reminder job (delayed by sla_days - 1)
        try {
            $sla = (int) \App\Models\Setting::getValue('sla_days', 2);
            // H-1 means schedule at (created_at + (sla_days - 1) days).
            // if sla_days == 1 then H-1 == creation time -> dispatch immediately.
            $delayDays = max(0, $sla - 1);
            $dispatchAt = $ticket->created_at->copy()->addDays($delayDays);

            if ($delayDays > 0) {
                \App\Jobs\SendSlaReminder::dispatch($ticket->id)->delay($dispatchAt);
            } else {
                \App\Jobs\SendSlaReminder::dispatch($ticket->id);
            }

            Log::info('SLA REMINDER SCHEDULED', ['ticket_id' => $ticket->id, 'delay_days' => $delayDays, 'dispatch_at' => $dispatchAt->toDateTimeString()]);
        } catch (\Throwable $e) {
            Log::error('SLA REMINDER SCHEDULE FAILED', ['ticket_id' => $ticket->id, 'err' => $e->getMessage()]);
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
                // queue the WA close notification
                \App\Jobs\SendWhatsappTemplate::dispatch(
                    $this->normalizePhone($ticket->hp),
                    'notifikasi_tiket_close',
                    'id',
                    [
                        $ticket->nama_pelapor,
                        $ticket->nomor_tiket,
                        optional($ticket->closing_at)->format('d F Y') ?? now()->format('d F Y'),
                        $ticket->closing_notes ?? '-',
                    ]
                );

                Log::info('WA CLOSE QUEUED', ['ticket_id' => $ticket->id]);
            } catch (\Throwable $e) {
                Log::error('WA CLOSE QUEUE FAILED', [
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
