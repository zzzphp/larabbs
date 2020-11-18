<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 获取用户所有通知
        $notifications = Auth::user()->notifications()->paginate(20);
        // 清除通知
        Auth::user()->markAsRead();
        return view('notifications.index', compact('notifications'));
    }
}
