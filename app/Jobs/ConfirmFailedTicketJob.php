<?php

namespace App\Jobs;

use App\Enums\TicketStatus;
use App\Infrastructure\Adapters\ExternalTicketConfirmAdapter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ConfirmFailedTicketJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Collection $tickets)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->tickets as $ticket) {
            $ticket->lockForUpdate();
            $ticket->state()->approve();
        }
    }
}
