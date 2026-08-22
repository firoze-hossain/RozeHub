<?php

use App\Services\ReleaseStorageService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rozehub:release-upload-cleanup {--hours=24}', function () {
    $hours = max(1, (int) $this->option('hours'));
    $deleted = app(ReleaseStorageService::class)->purgeStaleUploads($hours);
    $this->info("Removed {$deleted} stale release-upload file(s).");
})->purpose('Remove abandoned external release-upload chunks');

Schedule::command('rozehub:release-upload-cleanup --hours=24')->daily();
