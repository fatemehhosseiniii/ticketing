<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketRequest;
use App\Http\Resources\PaginateResource;
use App\Models\Ticket;
use App\Services\Response;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Make tickets list
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index()
    {
        $user = auth()->user();
        //load tickets
        $tickets = Ticket::where('creator_id', $user->id)
            ->latest()
            ->paginate(config('settings.paginate'));

        //make Response and Return result
        return Response::success([
            'tickets' => $tickets->toResourceCollection(),
            'links' => new PaginateResource($tickets),
        ]);
    }

    public function show($ticketCode)
    {
        //find Ticket
        $user = auth()->user();
        $ticket = $user->tickets()->where('code', $ticketCode)->sole();

        //make Response and Return result
        return Response::success(['ticket' => $ticket->toResource()->additional(['showContent' => true])]);
    }

    /**
     * Save New ticket
     * @param TicketRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TicketRequest $request)
    {
        //Create new
        $user = auth()->user();

        $ticket = $user->tickets()->create($request->validated());

        //make Response and Return result
        return Response::success(['ticket' => $ticket->toResource()]);

    }


    public function destroy($ticketCode)
    {
        //find Ticket
        $user = auth()->user();
        $user->tickets()
            ->where('status', TicketStatus::New)
            ->where('code', $ticketCode)->delete();

        return Response::success();
    }
}
