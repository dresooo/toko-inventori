<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class AdminNotificationController extends Controller
{
    /**
     * Simpan notifikasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type'    => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        $notif = Notification::create([
            'user_id'    => $request->user_id,
            'type'       => $request->type,
            'message'    => $request->message,
            'is_read'    => 0,
            'created_at' => now(),
        ]);

        return response()->json($notif, 201);
    }

    /**
     * Ambil semua notifikasi untuk user login
     */
    public function index(Request $request)
{
    $notifs = Notification::where('user_id', $request->user()->id)
                         ->orderBy('created_at', 'desc')
                         ->get();
    return response()->json($notifs);
}

    /**
     * Tandai notifikasi sudah dibaca
     */
    public function markAsRead($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->is_read = 1;
        $notif->save();

        return response()->json(['success' => true, 'message' => 'Notifikasi dibaca']);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi dihapus']);
    }

    /**
     * Buat notifikasi untuk semua admin
     */
    public function notifyAllAdmins($type, $message)
    {
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => $type,
                'message'    => $message,
                'is_read'    => 0,
                'created_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Notifikasi dikirim ke semua admin']);
    }
 public function markAllRead(Request $request)
    {
        DB::table('notifications')
            ->where('user_id', $request->user()->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca'
        ]);
    }
    
}


