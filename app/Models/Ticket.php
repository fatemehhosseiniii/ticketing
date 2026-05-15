<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Policies\TicketPolicy;
use App\Services\Tickets\TicketState;
use App\Services\Tickets\TicketStateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'subject', 'description', 'file_src', 'status', 'status_message', 'creator_id', 'expert_id', 'checked_at'])]
#[Hidden(['creator_id', 'expert_id'])]
#[UsePolicy(TicketPolicy::class)]
class Ticket extends Model
{

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'checked_at' => 'datetime'
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function state(): TicketState
    {
        return TicketStateFactory::make($this);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $code = 100;
            $query = self::query();
            if ($query->count()) {
                $code = $query->max('code') + 1;
            }
            $model->code = $code;
        });
    }
}
