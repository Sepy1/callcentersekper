<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    // mark notification as read (if belongs to auth user) then redirect to stored link
    public function open($id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // baca record langsung dari tabel (hindari masalah model/table name)
        $notif = DB::table('notifications_custom')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notif) {
            return redirect(url('/'));
        }

        // capture before-state
        $beforeIsRead = isset($notif->is_read) ? (int)$notif->is_read : null;
        $affected = 0;
        try {
            $affected = DB::table('notifications_custom')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->update([
                    'is_read' => 1,
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('Notif update error', [
                'id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            $affected = 0;
        }

        // re-read row to verify
        $notifAfter = DB::table('notifications_custom')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        $afterIsRead = isset($notifAfter->is_read) ? (int)$notifAfter->is_read : null;

        // if update didn't actually change the value, return debug JSON for inspection
        if (empty($affected) || $afterIsRead !== 1) {
            Log::warning('Notification update not applied', [
                'id' => $id,
                'user_id' => $user->id,
                'before' => $beforeIsRead,
                'affected' => $affected,
                'after' => $afterIsRead,
                'connection' => DB::getDefaultConnection(),
            ]);

            return response()->json([
                'error' => 'update_failed',
                'id' => $id,
                'user_id' => $user->id,
                'before_is_read' => $beforeIsRead,
                'affected_rows' => $affected,
                'after_is_read' => $afterIsRead,
                'connection' => DB::getDefaultConnection(),
                'record' => $notifAfter,
            ], 500);
        }

        $target = $notif->link ?: url('notifications');
        return redirect($target);
    }

    // debug: tunjukkan record notifikasi + unread count untuk user saat ini
    public function debug($id)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'unauthenticated'], 401);

        $notif = DB::table('notifications_custom')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        $unreadCount = DB::table('notifications_custom')
            ->where('user_id', $user->id)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'notification' => $notif,
            'unread_count' => $unreadCount,
        ]);
    }

    // return unread count (useful for quick checks)
    public function count()
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'unauthenticated'], 401);

        $unreadCount = DB::table('notifications_custom')
            ->where('user_id', $user->id)
            ->where('is_read', 0)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    // mark read via POST (AJAX)
    public function markRead(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }

        try {
            $affected = DB::table('notifications_custom')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->update([
                    'is_read' => 1,
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('markRead error', ['id'=>$id,'user_id'=>$user->id,'err'=>$e->getMessage()]);
            $affected = 0;
        }

        $notif = DB::table('notifications_custom')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'success' => (bool)$affected,
            'affected' => $affected,
            'notification' => $notif,
        ]);
    }
}
