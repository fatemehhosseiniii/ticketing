<?php

namespace App\Services\Tickets;


use App\Enums\TicketStatus;
use App\Models\Ticket;

abstract class BaseTicketState implements TicketState
{
    public function __construct(protected Ticket $ticket)
    {
        //
    }

    public function reject(string|null $reason): void
    {
        $this->change(TicketStatus::Rejected, $reason);
    }

    protected function change(TicketStatus $status, string|null $reason=null): void
    {
        $this->ticket->update([
            'status' => $status,
            'expert_id' => auth()->id(),
            'status_message' => $reason
        ]);
    }
}
