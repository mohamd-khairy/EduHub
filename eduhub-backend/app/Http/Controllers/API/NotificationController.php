<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()->unreadNotifications();
        return $this->success($notifications->paginate(request('per_page', 15)));
    }

    public function All(Request $request)
    {
        $notifications = auth()->user()->notifications();
        return $this->success($notifications->get());
    }

    public function readNotification(Request $request)
    {
        $user = auth()->user();
        $user->unreadNotifications->where('id', $request->id)->markAsRead();
        return $this->success(true);
    }

    public function readAllNotification(Request $request)
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();
        return $this->success(true);
    }
}
