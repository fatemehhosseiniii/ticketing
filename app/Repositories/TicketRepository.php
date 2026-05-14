<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\GlobalRepository;
use App\Services\FileUploadService;
use http\Env\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketRepository
{
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->fileUploadService = new FileUploadService();
    }

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

    public function destroy($ticket): void
    {
        if (!empty($ticket->file_src))
            $this->fileUploadService->setFile(null, $ticket->file_src, true);

        $ticket->delete();
    }


}
