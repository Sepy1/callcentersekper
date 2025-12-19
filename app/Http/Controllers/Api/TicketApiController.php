<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketAssignedNotification;

class TicketApiController extends Controller
{
    // show ticket by numeric id or by nomor_tiket
    public function show($id)
    {
        try {
            if (is_numeric($id)) {
                $ticket = Ticket::with('officers')->find($id);
            } else {
                $ticket = Ticket::with('officers')->where('nomor_tiket', $id)->first();
            }

            if (!$ticket) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
            }

            return response()->json(['success' => true, 'ticket' => $ticket], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // create ticket via API
    public function store(Request $request)
    {
        $rules = [
            'nama_pelapor' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'kategori' => 'required|string|max:255',
            'tipe_pelapor' => 'nullable|string|max:255',
            'officer' => 'nullable|string|max:255',
            'status' => 'required|string|in:open,in_progress,closed',
            'judul' => 'required|string|max:255',
            'detail' => 'required|string',
            'officer_ids' => 'nullable|string',
        ];

        if ($request->input('tipe_pelapor') === 'Nasabah') {
            $rules = array_merge($rules, [
                'id_ktp' => 'required|string|max:100',
                'nomor_rekening' => 'required|string|max:100',
                'hp' => 'required|string|max:20',
            ]);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $date = now()->format('Ymd');
            $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $nomor_tiket = 'JTG-' . $date . '-' . $random;

            $ticket = new Ticket();
            $ticket->nomor_tiket = $nomor_tiket;
            $ticket->nama_pelapor = $validated['nama_pelapor'];
            $ticket->email = $validated['email'];
            $ticket->kategori = $validated['kategori'];
            $ticket->tipe_pelapor = $validated['tipe_pelapor'] ?? null;

            if ($ticket->tipe_pelapor === 'Nasabah') {
                $ticket->id_ktp = $validated['id_ktp'];
                $ticket->nomor_rekening = $validated['nomor_rekening'];
                $ticket->hp = $validated['hp'];
            } else {
                if ($request->filled('hp')) $ticket->hp = $request->input('hp');
            }

            $ticket->officer = $request->input('officer') ?? null;
            $ticket->status = $validated['status'];
            $ticket->judul = $validated['judul'];
            $ticket->detail = $validated['detail'];
            $ticket->save();

            // handle officer_ids pivot (comma separated)
            if ($request->filled('officer_ids')) {
                $ids = array_filter(array_map('trim', explode(',', $request->input('officer_ids'))));
                if (!empty($ids)) {
                    $attachData = [];
                    foreach ($ids as $oid) {
                        $attachData[$oid] = [
                            'status' => 'assigned',
                            'lampiran' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    $ticket->officers()->attach($attachData);
                    // update human-readable officer names
                    $names = User::whereIn('id', $ids)->pluck('name')->toArray();
                    $ticket->officer = implode(', ', $names);
                    $ticket->save();
                }
            }

            DB::commit();

            // notify admins/qa and pelapor via email
            try {
                $users = User::whereIn('role', ['admin','qa'])->get();
                NotificationFacade::send($users, new TicketCreatedNotification($ticket, 'admin'));
            } catch (\Throwable $e) { \Log::error('send ticket created email (api) failed: ' . $e->getMessage()); }

            try {
                if (!empty($ticket->email)) {
                    NotificationFacade::route('mail', $ticket->email)->notify(new TicketCreatedNotification($ticket, 'pelapor'));
                }
            } catch (\Throwable $e) { \Log::error('send ticket created email to pelapor (api) failed: ' . $e->getMessage()); }

            // notify assigned officers via email if any
            try {
                if (!empty($ids) && is_array($ids)) {
                    $users = User::whereIn('id', $ids)->get();
                    if ($users->isNotEmpty()) {
                        NotificationFacade::send($users, new TicketAssignedNotification($ticket));
                    }
                }
            } catch (\Throwable $e) { \Log::error('send ticket assigned email (api) failed: ' . $e->getMessage()); }
            return response()->json(['success' => true, 'ticket' => $ticket->fresh()->load('officers')], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create ticket'], 500);
        }
    }
}
