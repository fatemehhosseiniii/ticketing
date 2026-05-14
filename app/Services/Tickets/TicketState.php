<?php

namespace App\Services\Tickets;


interface TicketState
{
    public function approve(): void;
}

