<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketRejectionRequest;
use App\Models\Ticket;
use App\Services\Response;
use Illuminate\Http\Request;

class TicketApprovalController extends Controller
{
    public function __invoke(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $ticket->state()->approve();
        return Response::success();
    }
}
