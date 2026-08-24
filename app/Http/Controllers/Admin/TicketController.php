<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\RecycleTicket;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketClosedNotification;

class TicketController extends Controller
{
    public function show(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        // attach CIF list for view (if id_ktp matches nasabahs)
        try {
            $ticket->cifs = [];
            if (!empty($ticket->id_ktp)) {
                $ticket->cifs = \App\Models\Nasabah::where('no_ktp', $ticket->id_ktp)->pluck('cif')->toArray();
            }
        } catch (\Throwable $e) {
            \Log::warning('attach cifs failed: ' . $e->getMessage());
            $ticket->cifs = [];
        }
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
        $categories = Category::orderBy('name')->get();

        return view('admin.tickets', compact('tickets', 'categories'));
    }

    // Tindak lanjut GET & POST
    public function tindakLanjut(Request $request)
    {
        $nomorTiket = $request->input('nomor_tiket');
        $ticketId = $request->input('ticket_id');
        $ticket = null;
        $officers = User::where('role', 'officer')->get();

        // jika yang membuka adalah QA dan ada nomor tiket atau ticket_id, arahkan ke QA tindak-lanjut dengan param yang sama
        if (auth()->check() && auth()->user()->role === 'qa' && ($nomorTiket || $ticketId)) {
            $params = [];
            if ($ticketId) $params['ticket_id'] = $ticketId;
            if ($nomorTiket) $params['nomor_tiket'] = $nomorTiket;
            return redirect()->route('qa.tindak-lanjut', $params);
        }

        // Handle POST for eskalasi/tagging or status update
        if ($request->isMethod('post') && ($nomorTiket || $ticketId)) {
            if ($ticketId) {
                $ticket = Ticket::find($ticketId);
            } else {
                $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
            }

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

                        // notify newly assigned officers
                        $titleAssign = "Ditetapkan ke tiket: {$ticket->nomor_tiket}";
                        $msgAssign = "Anda ditugaskan pada tiket {$ticket->nomor_tiket}. Judul: " . \Illuminate\Support\Str::limit($ticket->judul ?? '', 120);
                        // link langsung ke halaman tindak-lanjut officer yang membuka tiket tersebut
                        $linkAssign = url('officer/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket);
                        $this->notifyOfficers($toAdd, $titleAssign, $msgAssign, $linkAssign, ['ticket_id' => $ticket->id, 'action' => 'assigned']);

                        // send email to newly assigned officers
                        try {
                            $users = User::whereIn('id', $toAdd)->get();
                            NotificationFacade::send($users, new TicketAssignedNotification($ticket));
                        } catch (\Throwable $e) {
                            \Log::error('send ticket assigned email failed: ' . $e->getMessage());
                        }

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

                        // notify pelapor by email that ticket is closed
                        try {
                            if (!empty($ticket->email)) {
                                NotificationFacade::route('mail', $ticket->email)->notify(new TicketClosedNotification($ticket));
                            }
                        } catch (\Throwable $e) {
                            \Log::error('send ticket closed email failed: ' . $e->getMessage());
                        }
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
            if ($ticketId) {
                $ticket = Ticket::find($ticketId);
            } else {
                $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
            }

            // Post-Redirect-Get: avoid browser re-submitting the POST on reload
            if ($ticket) {
                return redirect()->route('admin.tindak-lanjut', ['ticket_id' => $ticket->id, 'nomor_tiket' => $ticket->nomor_tiket]);
            } else {
                return redirect()->route('admin.tindak-lanjut');
            }
        } else if ($nomorTiket) {
            $ticket = Ticket::where('nomor_tiket', $nomorTiket)->first();
        } else if ($ticketId) {
            $ticket = Ticket::find($ticketId);
        }

        // attach CIF list for view
        try {
            if ($ticket) {
                $ticket->cifs = [];
                if (!empty($ticket->id_ktp)) {
                    $ticket->cifs = \App\Models\Nasabah::where('no_ktp', $ticket->id_ktp)->pluck('cif')->toArray();
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('attach cifs failed: ' . $e->getMessage());
            if ($ticket) $ticket->cifs = [];
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
            'hp' => 'required|string|max:20',
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
        // HP is a top-level field now (always save if provided)
        $ticket->hp = $request->input('hp');
        // nasabah fields
        if ($request->input('tipe_pelapor') === 'Nasabah') {
            $ticket->id_ktp = $request->input('id_ktp');
            $ticket->nomor_rekening = $request->input('nomor_rekening');
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
        // validate KTP against nasabahs table and mark as nasabah if matches
        try {
            if (!empty($ticket->id_ktp)) {
                $matches = \App\Models\Nasabah::where('no_ktp', $ticket->id_ktp)->pluck('cif')->toArray();
                if (!empty($matches)) {
                    $ticket->is_nasabah = true;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('nasabah lookup failed: ' . $e->getMessage());
        }
        $ticket->officer = implode(', ', $officerNames);
        $ticket->status = $request->input('status');
        $ticket->judul = $request->input('judul');
        $ticket->detail = $request->input('detail');
        $ticket->save();

        \Log::info('TICKET CREATED', [
            'ticket_id' => $ticket->id,
            'nomor_tiket' => $ticket->nomor_tiket,
            'created_by' => auth()->id(),
            'status' => $ticket->status,
        ]);

        // log creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'action' => 'ticket_created',
            'detail' => 'Ticket created with nomor_tiket=' . $ticket->nomor_tiket,
            'ip' => $request->ip(),
        ]);

        // notify admin and qa about new ticket
        $title = "Tiket baru: {$ticket->nomor_tiket}";
        $msg = ($ticket->judul ? $ticket->judul . ' — ' : '') . \Illuminate\Support\Str::limit($ticket->detail ?? '', 120);
        $link = url('admin/tickets/' . $ticket->id);
        $this->notifyAdminsAndQa($title, $msg, $link, ['ticket_id' => $ticket->id, 'source' => 'form']);

        // send email notifications: admins/qa
        try {
            $users = User::whereIn('role', ['admin','qa'])->get();
            NotificationFacade::send($users, new TicketCreatedNotification($ticket, 'admin'));
        } catch (\Throwable $e) {
            \Log::error('send ticket created email failed: ' . $e->getMessage());
        }

        // send email to pelapor (by email address)
        try {
            if (!empty($ticket->email)) {
                NotificationFacade::route('mail', $ticket->email)->notify(new TicketCreatedNotification($ticket, 'pelapor'));
                \Log::info('TICKET EMAIL QUEUED', [
                    'ticket_id' => $ticket->id,
                    'nomor_tiket' => $ticket->nomor_tiket,
                    'recipient_type' => 'pelapor',
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('send ticket created email to pelapor failed: ' . $e->getMessage());
        }

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
            'hp' => 'nullable|string|max:20',
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
        if ($request->filled('hp')) $ticket->hp = $request->input('hp');
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

    public function chartData(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $role = $request->input('role', 'admin');

        // Prepare date period
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        $labels = [];
        $values = [];

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $labels[] = $date->format('d M Y');

            if ($role === 'officer') {
                // Count tickets created on date where the ticket is assigned to current officer
                $cnt = DB::table('tickets')
                    ->join('ticket_officer', 'tickets.id', '=', 'ticket_officer.ticket_id')
                    ->whereDate('tickets.created_at', $d)
                    ->where('ticket_officer.officer_id', auth()->id())
                    ->distinct('tickets.id')
                    ->count('tickets.id');
            } elseif ($role === 'qa') {
                // Count tickets created on date where ALL assigned officers have status = 'proses_qa' (case-insensitive)
                $cnt = DB::table('tickets')
                    ->whereDate('tickets.created_at', $d)
                    ->whereRaw('(select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id) > 0')
                    ->whereRaw("(select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id and LOWER(ticket_officer.status) = 'proses_qa') = (select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id)")
                    ->count();
            } else {
                // admin / default: count all tickets created on date
                $cnt = DB::table('tickets')
                    ->whereDate('created_at', $d)
                    ->count();
            }

            $values[] = (int) $cnt;
        }
        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    // Download nominatif XLS (Excel-friendly HTML table) — semua kolom tickets
    public function downloadNominatif(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil semua kolom tabel tickets
        $columns = Schema::getColumnListing('tickets');

        $filename = 'nominatif_' . $startDate . '_to_' . $endDate . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets, $columns) {
            // Output HTML table which Excel can open directly
            echo "<table border='1'><thead><tr>";
            foreach ($columns as $col) {
                echo '<th>' . htmlentities($col) . '</th>';
            }
            echo "</tr></thead><tbody>";

            foreach ($tickets as $t) {
                echo "<tr>";
                foreach ($columns as $col) {
                    $val = $t->{$col} ?? '';
                    if (is_array($val) || is_object($val)) $val = json_encode($val);
                    echo '<td>' . htmlentities((string)$val) . '</td>';
                }
                echo "</tr>";
            }

            echo "</tbody></table>";
        };

        return response()->stream($callback, 200, $headers);
    }

    // Generate PDF report (monthly / custom range)
    public function generatePdf(Request $request)
    {
        // Accept start_date/end_date from query; if missing or empty, default to last 30 days
        $reqStart = $request->input('start_date');
        $reqEnd = $request->input('end_date');

        $startDate = $reqStart && trim($reqStart) !== '' ? $reqStart : now()->subDays(30)->format('Y-m-d');
        $endDate = $reqEnd && trim($reqEnd) !== '' ? $reqEnd : now()->format('Y-m-d');

        $tickets = Ticket::with('officers')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // Rekapitulasi
        $jumlahDiterima = $tickets->count();
        $jumlahSelesai = $tickets->where('status', 'closed')->count() + $tickets->where('status', 'resolved')->count();
        $jumlahProses = $tickets->whereIn('status', ['open', 'in_progress'])->count();

        // Rata-rata waktu penyelesaian (jam) untuk tiket yang berstatus 'closed'
        // Compute average resolution hours for closed tickets using DB (more reliable)
        try {
            $avgRow = DB::table('tickets')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('closing_at')
                ->whereRaw("LOWER(COALESCE(status, '')) = ?", ['closed'])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closing_at)) as avg_hours, COUNT(*) as cnt')
                ->first();

            $avgHours = $avgRow && $avgRow->cnt ? round((float)$avgRow->avg_hours, 2) : 0;
        } catch (\Exception $e) {
            // Fallback to collection-based calculation if DB function not available
            $closedTickets = $tickets->filter(function($t){
                return ($t->status === 'closed') && $t->created_at && $t->closing_at;
            });

            $durations = $closedTickets->map(function($t){
                return max(0, $t->closing_at->diffInHours($t->created_at));
            });

            $avgHours = $durations->count() ? round($durations->average(), 2) : 0;
        }

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tickets' => $tickets,
            'jumlahDiterima' => $jumlahDiterima,
            'jumlahSelesai' => $jumlahSelesai,
            'jumlahProses' => $jumlahProses,
            'avgHours' => $avgHours,
        ];

        // If barryvdh/laravel-dompdf is installed, use it. Otherwise return HTML view for manual printing.
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf') || class_exists('\Barryvdh\DomPDF\PDF')) {
            try {
                $pdf = \PDF::loadView('admin.reports.monthly', $data)->setPaper('a4', 'portrait');
                $filename = 'laporan_tiket_' . $startDate . '_to_' . $endDate . '.pdf';
                return $pdf->download($filename);
            } catch (\Exception $e) {
                return response()->view('admin.reports.monthly', $data);
            }
        }

        // Fallback: render HTML view (user can print to PDF from browser)
        return view('admin.reports.monthly', $data);
    }
}
