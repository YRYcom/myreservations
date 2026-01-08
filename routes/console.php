<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reservations:send-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();
