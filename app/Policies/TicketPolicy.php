<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::User,
            UserRole::LevelOne,
            UserRole::LevelTwo,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($ticket->creator_id === $user->id)
            return true;
        else {
            if ($user->role === UserRole::LevelOne &&
                (in_array($ticket->status, [TicketStatus::New, TicketStatus::Accepted]) || ($ticket->status === TicketStatus::Rejected && $ticket->expert_id === $user->id)))
                return true;
            elseif ($user->role === UserRole::LevelTwo &&
                (in_array($ticket->status, [TicketStatus::Accepted, TicketStatus::Send, TicketStatus::Completed]) || ($ticket->status === TicketStatus::Rejected && $ticket->expert_id === $user->id)))
                return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::User;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->role === UserRole::LevelOne && $ticket->status === TicketStatus::New)
            return true;
        elseif ($user->role === UserRole::LevelTwo && $ticket->status === TicketStatus::Accepted)
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::User && $ticket->status === TicketStatus::New;
    }

}
