<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\Notify;
use App\Models\User;
use App\Jobs\SendSms;
use App\Models\Admin;
use App\Jobs\AdminNotify;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
       return view('admin.notifications.index');
    }


    public function sendNotifications(Request $request)
    {
        if ($request->user_type == 'all') {
            $rows = User::get();
        } else if ($request->user_type == 'clients') {
            $rows = User::where('type', 'client')->get();
        } else if ($request->user_type == 'delivery') {
            // Respect delivery users' notification preference
            $rows = User::where('type', 'delivery')->where('is_notify', true)->get();
        } else if ($request->user_type == 'admins') {
            $rows = Admin::get();
        }

        if ($request->type == 'notify') {
            if ($request->user_type == 'admins') {
                dispatch(new AdminNotify($rows, $request));
            } else {
                dispatch(new Notify($rows, $request));
            }
        } else if ($request->type == 'email') {
            dispatch(new SendEmailJob($rows->pluck('email'), $request));
        } else {
            dispatch(new SendSms($rows->pluck('phone')->toArray(), $request->message));
        }

        return response()->json();
    }
}
