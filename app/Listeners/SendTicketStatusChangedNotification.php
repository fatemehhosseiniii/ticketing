<?php

namespace App\Listeners;

use App\Actions\SendTicketStatusNotification;
use App\Events\TicketStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class SendTicketStatusChangedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SendTicketStatusNotification $action)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketStatusChanged $event): void
    {
        $this->action->handle(
            $event->ticket,
            $event->oldStatus,
            $event->newStatus
        );
    }
}
