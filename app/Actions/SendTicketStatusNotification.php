<?php

namespace App\Actions;

use App\Models\Ticket;
use App\Notifications\TicketStatusChangedNotification;

class SendTicketStatusNotification
{
    public function handle(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        $ticket->user->notify(new TicketStatusChangedNotification($ticket, $oldStatus, $newStatus));
    }
}
