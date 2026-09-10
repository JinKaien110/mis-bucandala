<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backfill missing cases rows for blotters so analytics (Case Types) can count incident types.
Artisan::command('cases:backfill-from-blotters', function () {
    $this->call(\App\Console\Commands\BackfillCasesFromBlotters::class, [
        // allow optional --dry-run to be forwarded automatically by artisan
        '--dry-run' => $this->option('dry-run'),
    ]);
})->purpose('Backfill missing cases from blotters for analytics');

