<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\RecycleTicket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function show(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function index(Request $request)
    {
        $query = Ticket::query();

        // Search
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($sub) use ($q) {
                $sub->where('nomor_tiket', 'like', "%$q%")
                    ->orWhere('nama_pelapor', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('judul', 'like', "%$q%")
                    ->orWhere('detail', 'like', "%$q%")
                    ->orWhere('officer', 'like', "%$q%")
                    ->orWhere('kategori', 'like', "%$q%")
                    ->orWhere('status', 'like', "%$q%");
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        // Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }
        $tickets = $query->orderBy('created_at', 'desc')->paginate(7)->withQueryString();
        return view('admin.tickets', compact('tickets'));
    }

    // Tindak lanjut GET & POST
    public function tindakLanjut(Request $request)
    {
        $nomorTiket = $request->input('nomor_tiket');
        $ticket = null;
        $officers = User::where('role', 'officer')->get();

        // jika yang membuka adalah QA dan ada nomor tiket, arahkan ke QA tindak-lanjut
        if (auth()->check() && auth()->user()->role === 'qa' && $nomorTiket) {
            return redirect()->route('qa.tindak-lanjut', ['nomor_tiket' => $nomorTiket]);
        }

        // Handle POST for eskalasi/tagging or status update
        if ($request->isMethod('post') && $nomorTiket) {
            $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
            if ($ticket) {
                // Assign ke multi officer (array of officer_id) - simpan ke tabel pivot
                if ($request->filled('officer_ids')) {
                    $officerIds = $request->input('officer_ids');
                    if (is_string($officerIds)) {
                        $officerIds = array_filter(explode(',', $officerIds));
                    }
                    $officerIds = array_values(array_map('trim', $officerIds));

                    $currentIds = \DB::table('ticket_officer')
                        ->where('ticket_id', $ticket->id)
                        ->pluck('officer_id')
                        ->map(fn($v) => (string)$v)
                        ->toArray();

                    $toAdd = array_diff($officerIds, $currentIds);
                    $toRemove = array_diff($currentIds, $officerIds);

                    if (!empty($toAdd)) {
                        $attachData = [];
                        foreach ($toAdd as $oid) {
                            $attachData[$oid] = [
                                'status' => 'assigned',
                                'lampiran' => null,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                        $ticket->officers()->attach($attachData);

                        // log additions
                        foreach ($toAdd as $oid) {
                            ActivityLog::create([
                                'user_id' => auth()->id(),
                                'ticket_id' => $ticket->id,
                                'action' => 'officer_assigned',
                                'detail' => 'Assigned officer_id=' . $oid,
                                'ip' => $request->ip(),
                            ]);
                        }
                    }

                    if (!empty($toRemove)) {
                        $ticket->officers()->detach($toRemove);

                        // log removals
                        foreach ($toRemove as $oid) {
                            ActivityLog::create([
                                'user_id' => auth()->id(),
                                'ticket_id' => $ticket->id,
                                'action' => 'officer_unassigned',
                                'detail' => 'Unassigned officer_id=' . $oid,
                                'ip' => $request->ip(),
                            ]);
                        }
                    }

                    $finalIds = $ticket->officers()->pluck('users.name')->toArray();
                    $ticket->officer = implode(', ', $finalIds);
                    $ticket->status = 'in_progress';
                    $ticket->save();

                    ActivityLog::create([
                        'user_id' => auth()->id(),
                        'ticket_id' => $ticket->id,
                        'action' => 'ticket_assigned_officers_updated',
                        'detail' => 'Officer list updated: ' . $ticket->officer,
                        'ip' => $request->ip(),
                    ]);
                }

                // Update status (masih ke tabel tickets)
                if ($request->filled('status')) {
                    $newStatus = $request->input('status');
                    if ($newStatus === 'closed') {
                        $oldStatus = $ticket->status;
                        $ticket->status = 'closed';
                        if ($request->filled('closing_at')) {
                            $ticket->closing_at = $request->input('closing_at');
                        } else {
                            $ticket->closing_at = now();
                        }
                        if ($request->filled('tindak_lanjut_closing')) {
                            $ticket->closing_notes = $request->input('tindak_lanjut_closing');
                        }
                        if ($request->filled('media_closing')) {
                            $ticket->media_closing = $request->input('media_closing');
                        }
                        $ticket->save();

                        ActivityLog::create([
                            'user_id' => auth()->id(),
                            'ticket_id' => $ticket->id,
                            'action' => 'status_changed',
                            'detail' => "Status: {$oldStatus} -> closed; closing_notes: " . ($ticket->closing_notes ?? ''),
                            'ip' => $request->ip(),
                        ]);
                    } else {
                        $oldStatus = $ticket->status;
                        $ticket->status = $newStatus;
                        $ticket->save();

                        ActivityLog::create([
                            'user_id' => auth()->id(),
                            'ticket_id' => $ticket->id,
                            'action' => 'status_changed',
                            'detail' => "Status: {$oldStatus} -> {$newStatus}",
                            'ip' => $request->ip(),
                        ]);
                    }
                }
            }
            // Refresh ticket after update
            $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
        } else if ($nomorTiket) {
            $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
        }

        return view('admin.tindak-lanjut', compact('ticket', 'officers'));
    }

    // CREATE TICKET (POST)
    public function store(Request $request)
    {
        // base rules
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
        // when tipe_pelapor == Nasabah, require/add nasabah fields
        if ($request->input('tipe_pelapor') === 'Nasabah') {
            $rules = array_merge($rules, [
                'id_ktp' => 'required|string|max:100',
                'nomor_rekening' => 'required|string|max:100',
                'hp' => 'required|string|max:20',
                'nama_ibu' => 'nullable|string|max:255',
                'alamat' => 'nullable|string',
                'tempat_lahir' => 'nullable|string|max:255',
                'tgl_lahir' => 'nullable|date',
                'kode_kantor' => 'nullable|string|max:50',
                'upload_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'upload_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);
        }
        $request->validate($rules);

        // Generate nomor tiket: JTG-YYYYMMDD-XXXXX (random 5 char)
        $date = now()->format('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $nomor_tiket = 'JTG-' . $date . '-' . $random;

        // Multi assign officer
        $officerNames = [];
        if ($request->filled('officer_ids')) {
            $ids = array_filter(explode(',', $request->input('officer_ids')));
            $officerNames = User::whereIn('id', $ids)->pluck('name')->toArray();
        } elseif ($request->filled('officer')) {
            $officerNames = [$request->input('officer')];
        }

        $ticket = new Ticket();
        $ticket->nomor_tiket = $nomor_tiket;
        $ticket->nama_pelapor = $request->input('nama_pelapor');
        $ticket->email = $request->input('email');
        $ticket->kategori = $request->input('kategori');
        $ticket->tipe_pelapor = $request->input('tipe_pelapor');
        // nasabah fields
        if ($request->input('tipe_pelapor') === 'Nasabah') {
            $ticket->id_ktp = $request->input('id_ktp');
            $ticket->nomor_rekening = $request->input('nomor_rekening');
            $ticket->hp = $request->input('hp');
            $ticket->nama_ibu = $request->input('nama_ibu');
            $ticket->alamat = $request->input('alamat');
            $ticket->tempat_lahir = $request->input('tempat_lahir');
            $ticket->tgl_lahir = $request->input('tgl_lahir');
            $ticket->kode_kantor = $request->input('kode_kantor');
            // file uploads
            if ($request->hasFile('upload_ktp')) {
                $ticket->upload_ktp = $request->file('upload_ktp')->store('tickets', 'public');
            }
            if ($request->hasFile('upload_bukti')) {
                $ticket->upload_bukti = $request->file('upload_bukti')->store('tickets', 'public');
            }
        }
        $ticket->officer = implode(', ', $officerNames);
        $ticket->status = $request->input('status');
        $ticket->judul = $request->input('judul');
        $ticket->detail = $request->input('detail');
        $ticket->save();

        // log creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'action' => 'ticket_created',
            'detail' => 'Ticket created with nomor_tiket=' . $ticket->nomor_tiket,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.tickets')->with('success', 'Tiket berhasil dibuat.');
    }

    // edit form
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('admin.tickets-edit', compact('ticket'));
    }

    // update ticket
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $rules = [
            'nama_pelapor' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'kategori' => 'required|string|max:255',
            'status' => 'required|string',
            'judul' => 'required|string|max:255',
            'detail' => 'required|string',
        ];
        $request->validate($rules);

        $old = $ticket->toArray();
        $ticket->nama_pelapor = $request->input('nama_pelapor');
        $ticket->email = $request->input('email');
        $ticket->kategori = $request->input('kategori');
        $ticket->status = $request->input('status');
        $ticket->judul = $request->input('judul');
        $ticket->detail = $request->input('detail');
        // optional fields
        if ($request->filled('officer')) $ticket->officer = $request->input('officer');
        $ticket->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'action' => 'ticket_updated',
            'detail' => 'Updated ticket fields; before: ' . \Illuminate\Support\Str::limit(json_encode($old), 800),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.tickets')->with('success', 'Tiket berhasil diperbarui.');
    }

    // destroy => move to recycle bin
    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // create recycle snapshot
        RecycleTicket::create([
            'original_ticket_id' => $ticket->id,
            'data' => $ticket->toArray(),
            'deleted_by' => auth()->id(),
            'deleted_ip' => $request->ip(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'action' => 'ticket_deleted',
            'detail' => 'Ticket moved to recycle bin (nomor_tiket=' . $ticket->nomor_tiket . ')',
            'ip' => $request->ip(),
        ]);

        $ticket->delete();


        return redirect()->route('admin.tickets')->with('success', 'Tiket Berhasil Di Delete');
    }
}
