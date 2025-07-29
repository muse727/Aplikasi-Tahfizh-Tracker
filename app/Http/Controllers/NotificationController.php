<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil notifikasi terbaru dan jumlah yang belum dibaca.
     */
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest()->take(5)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Menandai semua notifikasi yang belum dibaca sebagai "sudah dibaca".
     */
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->noContent();
    }
}
