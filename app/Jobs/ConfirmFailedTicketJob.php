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
        //call adapter
        $confirmAdapter = new ExternalTicketConfirmAdapter();

        foreach ($this->tickets as $ticket) {
            $ticket->lockForUpdate();

            $res = $confirmAdapter->confirm([
                'code' => $ticket->code,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
            ]);
            if ($res['successful'])
                $ticket->update([
                    'status' => TicketStatus::Completed,
                    'checked_at' => now()
                ]);

        }
    }
}
