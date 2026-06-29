<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogFailedLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $user = $event->user;
        \App\Models\LoginHistory::create([
            'user_id' => $user ? $user->id : null,
            'username' => $event->credentials['email'] ?? ($event->credentials['username'] ?? 'unknown'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_success' => false
        ]);
    }
}
