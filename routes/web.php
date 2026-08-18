<?php

use App\Http\Controllers\ReleaseDownloadController;
use App\Livewire\Hub;
use App\Livewire\ReleaseStudio;
use Illuminate\Support\Facades\Route;

Route::get('/', Hub::class)->name('hub');
Route::get('/studio', ReleaseStudio::class)->name('studio');
Route::get('/download/{release}', ReleaseDownloadController::class)->name('releases.download');
