<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\GlobalRepository;
use App\Services\FileUploadService;
use http\Env\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketRepository
{
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->fileUploadService = new FileUploadService();
    }

    /**
     * Load Tickets List
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function getList(User $user): LengthAwarePaginator
    {
        $query = Ticket::query();

        if ($user->role === UserRole::User)
            $query->where('creator_id', $user->id);
        elseif ($user->role === UserRole::LevelOne)
            $query->where(function (Builder $queryBuilder) use ($user) {
                $queryBuilder->whereIn('status', [TicketStatus::New, TicketStatus::Accepted])
                    ->orWhere(function (Builder $queryOr) use ($user) {
                        $queryOr->where('status', TicketStatus::Rejected)->where('expert_id', $user->id);
                    });
            });
        elseif ($user->role === UserRole::LevelTwo)
            $query->where(function (Builder $queryBuilder) use ($user) {
                $queryBuilder->whereIn('status', [TicketStatus::Accepted, TicketStatus::Send])
                    ->orWhere(function (Builder $queryOr) use ($user) {
                        $queryOr->where('status', TicketStatus::Rejected)->where('expert_id', $user->id);
                    });
            });


        return $query->latest()->paginate(config('settings.paginate'));
    }


    /**
     * Save New Ticket
     * @param $user
     * @param $data
     * @return mixed
     * @throws \Throwable
     */
    public function store($user, $data): mixed
    {
        return DB::transaction(function () use ($user, $data) {

            //check Exists file for Save
            if (!empty($data['file_src'])) {
                $data['file_src'] = $this->fileUploadService->setFile($data['file_src'], 'tickets/' . $user->id);
            }

            return $user->tickets()->create($data);

        });

    }

    /**
     * Remove Ticket
     * @param $ticket
     * @return void
     */
    public function destroy($ticket): void
    {
        if (!empty($ticket->file_src))
            $this->fileUploadService->setFile(null, $ticket->file_src, true);

        $ticket->delete();
    }


}
