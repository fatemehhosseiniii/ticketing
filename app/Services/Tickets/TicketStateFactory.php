<?php

namespace App\Services\Tickets;

use App\Enums\TicketStatus;
use App\Models\Ticket;

class TicketStateFactory
{
    public static function make(Ticket $ticket): TicketState
    {
        return match ($ticket->status) {
            TicketStatus::New => new NewState($ticket),
            TicketStatus::Accepted => new AcceptedState($ticket),
            default =>
            throw new \Exception('Invalid state'),
        };
    }
}
