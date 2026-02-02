<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     /** @var ClosureCommand $this */
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Shuffle every night at midnight so users see a "fresh" list daily
// Schedule::command('products:shuffle')->dailyAt('00:00');
Schedule::command('products:shuffle')->everySixHours();
