<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Jobs\ConfirmFailedTicketJob;
use App\Models\Ticket;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:confirm-failed-tickets-command')]
#[Description('Try to confirm Ticket')]
class ConfirmFailedTicketsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //find Ticket With SEND Status for try to Confirmed
        $tickets = Ticket::where('status', TicketStatus::Send)
            ->where('updated_at', '<=', now()->subHour())->get();
        if (!$tickets->isEmpty())
            ConfirmFailedTicketJob::dispatch($tickets);
    }
}
