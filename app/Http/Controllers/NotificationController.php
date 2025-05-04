<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    public function index()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        $notifications = auth()->user()->notifications()->get();
        $userType = auth()->user()->user_type;

        if (Auth::user()->user_type == 'admin') {
            return view('backend.notification.index', compact('notifications','userType'));
        }

        if (Auth::user()->user_type == 'seller') {
            return view('seller.notification.index', compact('notifications','userType'));
        }

        if (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.notification.index', compact('notifications','userType'));
        }
    }
}
