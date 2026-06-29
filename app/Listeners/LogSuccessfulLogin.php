<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        \App\Models\LoginHistory::create([
            'user_id' => $event->user->id,
            'username' => $event->user->email, // Assuming email is used as username
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_success' => true
        ]);
    }
}
