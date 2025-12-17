<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TindakLanjutController extends Controller
{
	// show page (used by route GET /officer/tindak-lanjut)
	public function index(Request $request)
	{
		$ticket = null;
		if ($request->filled('nomor_tiket')) {
			$ticket = Ticket::where('nomor_tiket', $request->input('nomor_tiket'))->first();
		}
		return view('officer.tindak-lanjut', compact('ticket'));
	}

	// handle POST from both forms (tindak_lanjut & status)
	public function proses(Request $request)
	{
		$request->validate([
			'nomor_tiket' => 'required|string',
			'tindak_lanjut' => 'nullable|string',
			'lampiran' => 'nullable|file|max:5120',
			'status' => 'nullable|string',
		]);

		$ticket = Ticket::where('nomor_tiket', $request->input('nomor_tiket'))->first();
		if (! $ticket) {
			return back()->with('error', 'Tiket tidak ditemukan.');
		}

		$officerId = auth()->id();
		$now = now();

		// Update tindak_lanjut (pivot tl + optional lampiran)
		if ($request->filled('tindak_lanjut') || $request->hasFile('lampiran')) {
			$update = [];
			if ($request->filled('tindak_lanjut')) {
				$update['tl'] = $request->input('tindak_lanjut');
			}
			if ($request->hasFile('lampiran')) {
				$file = $request->file('lampiran');
				$filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
				$path = $file->storeAs('ticket_officer', $filename, 'public');
				$update['lampiran'] = $path;
			}
			$update['updated_at'] = $now;

			$affected = DB::table('ticket_officer')
				->where('ticket_id', $ticket->id)
				->where('officer_id', $officerId)
				->update($update);

			ActivityLog::create([
				'user_id' => $officerId,
				'ticket_id' => $ticket->id,
				'action' => 'officer_tindak_lanjut',
				'detail' => 'Officer wrote tindak_lanjut' . ($update['lampiran'] ?? '') . (isset($update['tl']) ? ' — ' . Str::limit($update['tl'], 300) : ''),
				'ip' => $request->ip(),
			]);
		}

		// Update pivot status (officer status change)
		if ($request->filled('status')) {
			$newStatus = $request->input('status');
			$old = DB::table('ticket_officer')
				->where('ticket_id', $ticket->id)
				->where('officer_id', $officerId)
				->value('status');

			DB::table('ticket_officer')
				->where('ticket_id', $ticket->id)
				->where('officer_id', $officerId)
				->update(['status' => $newStatus, 'updated_at' => $now]);

			ActivityLog::create([
				'user_id' => $officerId,
				'ticket_id' => $ticket->id,
				'action' => 'officer_status_changed',
				'detail' => "Officer status: {$old} -> {$newStatus}",
				'ip' => $request->ip(),
			]);
		}

		return back()->with('success', 'Tindak lanjut berhasil disimpan.');
	}
}
