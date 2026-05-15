<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $notification = $this->resource;

        return $notification->data + [
                'type' => __('notifications.' . str_replace('App\\Notifications\\', '', $notification->type)),
                'created_at'=>$notification->created_at->format('Y-m-d H:i:s'),
            ];
    }
}
