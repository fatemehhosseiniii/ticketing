<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;


#[Fillable(['ticket_id', 'status', 'message'])]
class TicketLog extends Model
{
    //
}
