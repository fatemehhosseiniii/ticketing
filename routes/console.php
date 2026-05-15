<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('app:confirm-failed-tickets-command')->everyFiveMinutes();
