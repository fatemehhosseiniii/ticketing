<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tickets\TicketRejectionRequest;
use App\Models\Ticket;
use App\Services\Response;

class TicketRejectionController extends Controller
{
    public function __invoke(TicketRejectionRequest $request,Ticket $ticket)
    {
        $ticket->state()->reject($request->validated('status_message'));
        return Response::success();
    }
}
