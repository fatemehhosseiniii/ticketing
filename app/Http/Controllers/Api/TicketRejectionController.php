<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketRejectionRequest;
use App\Models\Ticket;
use App\Services\Response;
use Illuminate\Http\Request;

class TicketRejectionController extends Controller
{
    public function __invoke(TicketRejectionRequest $request,Ticket $ticket)
    {
        $ticket->state()->reject($request->validated('status_message'));
        return Response::success();
    }
}
