<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\User;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        // Konwertujemy model na tablicę, aby uniknąć problemów z serializacją Eloquent w kolejkach
        $this->user = $user->only(['id', 'login', 'email']);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user['email'])->send(new WelcomeMail($this->user));
    }
}
