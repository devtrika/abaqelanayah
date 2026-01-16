<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Notifications\NotifyUser ;

class Notify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users  , $request;

    public function __construct($users , $request)
    {
        $this->users    = $users;
        $this->request  = $request->all();
    }

    public function handle()
    {
        $users = collect($this->users);

        // Respect delivery users' notification preference
        $users = $users->filter(function ($user) {
            return !($user instanceof \App\Models\User && $user->type === 'delivery' && !$user->is_notify);
        });

        if ($users->isEmpty()) {
            return;
        }

        Notification::send($users, new NotifyUser($this->request));
    }
}
