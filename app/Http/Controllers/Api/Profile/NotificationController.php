<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\Profile\NotificationCollection;
use App\Services\Response;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __invoke()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(5);

        return Response::success([
            'notifications' => new NotificationCollection($notifications),
            'links' => new PaginateResource($notifications),
        ]);
    }
}
