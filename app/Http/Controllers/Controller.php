<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Notification;
use App\Models\User;
use App\Models\Ticket;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // constructor left intentionally simple; view sharing moved to AppServiceProvider
    }

    // create a single notification
    protected function createNotification(int $userId, string $title, string $message = null, string $link = null, array $data = [])
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            // fail silently
        }
    }

    // notify all admins and qa users (per-role link to tindak-lanjut with ticket_id)
    protected function notifyAdminsAndQa(string $title, string $message = null, string $link = null, array $data = [])
    {
        try {
            $users = User::whereIn('role', ['admin', 'qa'])->get(['id','role']);
            $ticket = null;
            if (!empty($data['ticket_id'])) {
                $ticket = Ticket::find($data['ticket_id']);
            }

            foreach ($users as $u) {
                $perLink = $link;
                if ($ticket) {
                    $query = '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket);
                    if ($u->role === 'qa') {
                        $perLink = url('qa/tindak-lanjut') . $query;
                    } else {
                        $perLink = url('admin/tindak-lanjut') . $query;
                    }
                } else {
                    // fallback to generic tindak-lanjut pages
                    $perLink = $perLink ?: ($u->role === 'qa' ? url('qa/tindak-lanjut') : url('admin/tindak-lanjut'));
                }

                $this->createNotification($u->id, $title, $message, $perLink, $data);
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    // notify multiple officers by id array
    protected function notifyOfficers(array $officerIds, string $title, string $message = null, string $link = null, array $data = [])
    {
        foreach ($officerIds as $oid) {
            $this->createNotification((int)$oid, $title, $message, $link, $data);
        }
    }

    // helper to notify admin + officers when a comment is added to a ticket
    protected function notifyTicketComment($ticket, $commentAuthorId = null, string $commentText = null)
    {
        try {
            $title = "Komentar baru pada tiket {$ticket->nomor_tiket}";
            $message = \Illuminate\Support\Str::limit($commentText ?? 'Ada komentar baru', 200);
            // notify admins + qa
            $this->notifyAdminsAndQa($title, $message, $link ?? null, ['ticket_id' => $ticket->id, 'type' => 'comment']);
            // notify assigned officers
            $officerIds = $ticket->officers()->pluck('users.id')->toArray();
            // buat link khusus officer -> tindak-lanjut officer dengan ticket_id & nomor_tiket
            $linkForOfficers = url('officer/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket);
            // exclude commenter if commenter is one of them
            if ($commentAuthorId) {
                $officerIds = array_filter($officerIds, fn($id) => $id != $commentAuthorId);
            }
            $this->notifyOfficers($officerIds, $title, $message, $linkForOfficers, ['ticket_id' => $ticket->id, 'type' => 'comment']);
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
