<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketRequest;
use App\Http\Resources\PaginateResource;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Services\Response;

class TicketController extends Controller
{

    public function __construct(private readonly TicketRepository $ticketRepository)
    {
    }

    /**
     * Make tickets list
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index()
    {
        $this->authorize('viewAny', Ticket::class);
        $user = auth()->user();
        //load tickets
        $tickets= $this->ticketRepository->getList($user);

        //make Response and Return result
        return Response::success([
            'tickets' => $tickets->toResourceCollection(),
            'links' => new PaginateResource($tickets),
        ]);
    }

    /**
     * View Ticket Detail
     * @param $ticketCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($ticketCode){
        //Find Ticket and check policy
        $ticket=Ticket::where('code', $ticketCode)->sole();
        $this->authorize('view', $ticket);

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

        $ticket = $this->ticketRepository->store($user, $request->validated());

        $ticket->refresh();

        //make Response and Return result
        return Response::success(['ticket' => $ticket->toResource()]);

    }


    /**
     * Remove New ticket
     * @param $ticketCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($ticketCode)
    {
        //Find Ticket and check policy
        $ticket=Ticket::where('code', $ticketCode)->sole();
        $this->authorize('delete', $ticket);

        $this->ticketRepository->destroy($ticket);

        return Response::success();
    }
}
