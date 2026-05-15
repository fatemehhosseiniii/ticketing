<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Response;

class TicketApprovalController extends Controller
{
    public function __invoke(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $ticket->state()->approve();
        return Response::success();
    }
}
