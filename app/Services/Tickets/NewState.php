<?php

namespace App\Services\Tickets;

use App\Enums\TicketStatus;

class NewState extends BaseTicketState
{
    public function approve(): void
    {
        $this->change(TicketStatus::Accepted);
    }
}
