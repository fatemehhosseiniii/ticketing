<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code','subject','description','file_src','status','status_message','creator_id','expert_id','checked_at'])]
#[Hidden(['creator_id', 'expert_id'])]
class Ticket extends Model
{
    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'checked_at'=>'timestamp'
        ];
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
