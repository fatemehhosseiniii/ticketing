<?php

namespace App\Services\Tickets;

use App\Enums\TicketStatus;
use App\Infrastructure\Adapters\ExternalTicketConfirmAdapter;
use App\Models\Ticket;

class AcceptedState extends BaseTicketState
{

    public function approve(): void
    {
        //Change status
        $this->change(TicketStatus::Send);

        //send to endpoint
        $adapter = new ExternalTicketConfirmAdapter();
        $res = $adapter->confirm([
            'code' => $this->ticket->code,
            'subject' => $this->ticket->subject,
            'description' => $this->ticket->description,
        ]);

        //Change status
        if ($res['successful'])
            $this->change(TicketStatus::Completed);

    }

}
