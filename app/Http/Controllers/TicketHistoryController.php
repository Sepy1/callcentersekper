<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketHistoryController extends Controller
{
    public function downloadPdf(Ticket $ticket)
    {
        $user = auth()->user();
        $allowed = in_array($user->role, ['admin', 'qa'], true)
            || ($user->role === 'officer' && $ticket->officers()->whereKey($user->id)->exists())
            || ($user->role === 'cabang' && $ticket->kode_kantor === $user->kode_kantor);

        abort_unless($allowed, 403);

        $history = ActivityLog::with('user')
            ->where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->get();

        return Pdf::loadView('tickets.history-pdf', compact('ticket', 'history'))
            ->setPaper('a4', 'portrait')
            ->download('riwayat-tiket-' . $ticket->nomor_tiket . '.pdf');
    }
}
