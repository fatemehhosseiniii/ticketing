<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ticket = $this->resource;
        return [
            'code' => $ticket->code,
            'subject' => $ticket->subject,
            'created_at' => $ticket->created_at->format('Y-m-d H:i'),

            'status' => $ticket->status?->toArray() ?? '',
            'status_message' => $this->when(!empty($this->additional['showContent']), $ticket->status_message),
            'checked_at' => $this->when(!empty($this->additional['showContent']),
            fn()=> !empty($ticket->checked_at) ? $ticket->checked_at->format('Y-m-d H:i') : null),

            'description' => $this->when(!empty($this->additional['showContent']), $ticket->description),
            'file_src' => $this->when(!empty($this->additional['showContent']),
                fn() => !empty($ticket->file_src) ? asset($ticket->file_src) : null),
        ];
    }
}
