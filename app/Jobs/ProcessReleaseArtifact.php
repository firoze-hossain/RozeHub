<?php
namespace App\Jobs;
use App\Models\Release;
use App\Services\ReleasePlatformService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ProcessReleaseArtifact implements ShouldQueue {
 use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 public int $tries=3;
 public function __construct(public int $releaseId){}
 public function handle(ReleasePlatformService $service):void{$release=Release::with('artifacts','githubRelease','project')->findOrFail($this->releaseId);$service->processRelease($release);}
}
