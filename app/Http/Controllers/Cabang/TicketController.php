<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;

class TicketController extends Controller
{
    public function dashboard()
    {
        $kodeKantor = $this->kodeKantor();
        $query = $this->ticketsForOffice($kodeKantor);

        return view('cabang.dashboard', [
            'kodeKantor' => $kodeKantor,
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('status', 'open')->count(),
            'inProgress' => (clone $query)->where('status', 'in_progress')->count(),
            'closed' => (clone $query)->whereIn('status', ['closed', 'resolved'])->count(),
            'latestTickets' => (clone $query)->latest()->limit(5)->get(),
        ]);
    }

    public function index(Request $request)
    {
        $kodeKantor = $this->kodeKantor();
        $query = $this->ticketsForOffice($kodeKantor);

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($sub) use ($search) {
                $sub->where('nomor_tiket', 'like', "%{$search}%")
                    ->orWhere('nama_pelapor', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        return view('cabang.tickets', [
            'tickets' => $query->latest()->paginate(10)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'kodeKantor' => $kodeKantor,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'hp' => ['required', 'string', 'max:20'],
            'kategori' => ['required', 'string', 'max:255'],
            'tipe_pelapor' => ['required', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'detail' => ['required', 'string'],
            'id_ktp' => ['nullable', 'required_if:tipe_pelapor,Nasabah', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'required_if:tipe_pelapor,Nasabah', 'string', 'max:100'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tgl_lahir' => ['nullable', 'date'],
            'upload_ktp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'upload_bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $ticket = new Ticket(Arr::except($validated, ['upload_ktp', 'upload_bukti']));
        $ticket->nomor_tiket = 'JTG-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $ticket->kode_kantor = $this->kodeKantor();
        $ticket->status = 'open';

        if ($request->hasFile('upload_ktp')) {
            $ticket->upload_ktp = $request->file('upload_ktp')->store('tickets', 'public');
        }
        if ($request->hasFile('upload_bukti')) {
            $ticket->upload_bukti = $request->file('upload_bukti')->store('tickets', 'public');
        }

        $ticket->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'action' => 'ticket_created_by_cabang',
            'detail' => 'Ticket created by branch ' . $ticket->kode_kantor,
            'ip' => $request->ip(),
        ]);

        $this->notifyAdminsAndQa(
            "Tiket baru: {$ticket->nomor_tiket}",
            $ticket->judul,
            url('admin/tickets/' . $ticket->id),
            ['ticket_id' => $ticket->id, 'source' => 'cabang', 'kode_kantor' => $ticket->kode_kantor]
        );

        try {
            $recipients = User::whereIn('role', ['admin', 'qa'])->get();
            NotificationFacade::send($recipients, new TicketCreatedNotification($ticket, 'admin'));
        } catch (\Throwable $e) {
            \Log::error('send branch ticket email failed', ['ticket_id' => $ticket->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('cabang.tickets')->with('success', 'Tiket berhasil dibuat.');
    }

    private function kodeKantor(): string
    {
        $kodeKantor = trim((string) auth()->user()->kode_kantor);
        abort_if($kodeKantor === '', 403, 'Kode kantor user cabang belum diatur.');

        return $kodeKantor;
    }

    private function ticketsForOffice(string $kodeKantor)
    {
        return Ticket::query()->whereRaw('BINARY TRIM(kode_kantor) = ?', [$kodeKantor]);
    }
}
