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
            'status' => 'nullable|string|in:open,in_progress,closed',
            'judul' => 'required|string|max:255',
            'detail' => 'required|string',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'kode_kantor' => 'nullable|string|max:100',
            'upload_ktp' => 'nullable|file',
            'upload_bukti' => 'nullable|file',
            'media_closing' => 'nullable|file',
            'closing_at' => 'nullable|date',
        ];

        if ($request->input('tipe_pelapor') === 'Nasabah') {
            $rules = array_merge($rules, [
                'id_ktp' => 'required|string|max:100',
                'nomor_rekening' => 'required|string|max:100',
                'hp' => 'required|string|max:20',
                'is_nasabah' => 'nullable|boolean',
            ]);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $ids = [];

            $date = now()->format('Ymd');
            $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $nomor_tiket = 'JTG-' . $date . '-' . $random;

            $ticket = new Ticket();
            $ticket->nomor_tiket = $nomor_tiket;
            $ticket->nama_pelapor = $validated['nama_pelapor'];
            $ticket->email = $validated['email'];
            $ticket->kategori = $validated['kategori'];
            $ticket->tipe_pelapor = $validated['tipe_pelapor'] ?? null;
            $ticket->judul = $validated['judul'];
            $ticket->detail = $validated['detail'];
            $ticket->status = $validated['status'] ?? 'open';

            // nasabah fields
            if ($ticket->tipe_pelapor === 'Nasabah') {
                $ticket->is_nasabah = true;
                $ticket->id_ktp = $validated['id_ktp'] ?? null;
                $ticket->nomor_rekening = $validated['nomor_rekening'] ?? null;
                $ticket->hp = $validated['hp'] ?? null;
            } else {
                $ticket->is_nasabah = false;
                if ($request->filled('hp')) $ticket->hp = $request->input('hp');
            }

            // other optional fields
            $ticket->nama_ibu = $validated['nama_ibu'] ?? null;
            $ticket->alamat = $validated['alamat'] ?? null;
            $ticket->tempat_lahir = $validated['tempat_lahir'] ?? null;
            $ticket->tgl_lahir = $validated['tgl_lahir'] ?? null;
            $ticket->kode_kantor = $validated['kode_kantor'] ?? null;

            if (!empty($validated['closing_at'])) {
                $ticket->closing_at = $validated['closing_at'];
            }

            $ticket->save();

            // store uploaded files if provided
            if ($request->hasFile('upload_ktp')) {
                try {
                    $path = $request->file('upload_ktp')->store('tickets', 'public');
                    $ticket->upload_ktp = $path;
                } catch (\Throwable $e) { \Log::error('upload_ktp store failed: ' . $e->getMessage()); }
            }
            if ($request->hasFile('upload_bukti')) {
                try {
                    $path = $request->file('upload_bukti')->store('tickets', 'public');
                    $ticket->upload_bukti = $path;
                } catch (\Throwable $e) { \Log::error('upload_bukti store failed: ' . $e->getMessage()); }
            }
            if ($request->hasFile('media_closing')) {
                try {
                    $path = $request->file('media_closing')->store('tickets', 'public');
                    $ticket->media_closing = $path;
                } catch (\Throwable $e) { \Log::error('media_closing store failed: ' . $e->getMessage()); }
            }

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
                }
            }

            $ticket->save();

            DB::commit();

            // ensure updated_at reflects final post-processing time (files/pivot attached)
            try {
                $ticket->refresh();
                $ticket->touch();
            } catch (\Throwable $e) {
                \Log::warning('touch ticket after commit failed: ' . $e->getMessage());
            }

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
            \Log::error('create ticket (api) failed: ' . $e->getMessage(), ['exception' => $e]);
            // Return exception message for debugging in local environment
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
