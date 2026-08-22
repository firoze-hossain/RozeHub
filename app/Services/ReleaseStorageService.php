<?php

namespace App\Services;

use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ReleaseStorageService
{
    public const MAX_BYTES = 8_589_934_592; // 8 GiB safety ceiling.

    public function disk()
    {
        return Storage::disk('releases');
    }

    public function storeUploadedFile(UploadedFile $file, SoftwareProject $project, array $metadata): array
    {
        $this->assertSize($file->getSize());

        $originalName = $this->safeFileName($file->getClientOriginalName());
        $relativePath = $this->relativePath($project, $metadata, $originalName);
        $this->disk()->makeDirectory(dirname($relativePath));

        $stored = $file->storeAs(dirname($relativePath), basename($relativePath), 'releases');
        if (!$stored) {
            throw new RuntimeException('The release package could not be stored.');
        }

        return $this->metadata($stored, $originalName);
    }

    public function consumeUploadToken(string $token, SoftwareProject $project, array $metadata): array
    {
        $token = $this->safeToken($token);
        $manifestPath = $this->manifestPath($token);
        $partPath = $this->partPath($token);

        if (!File::exists($manifestPath) || !File::exists($partPath)) {
            throw new RuntimeException('The uploaded package session has expired or no longer exists.');
        }

        $manifest = json_decode((string) File::get($manifestPath), true);
        if (!is_array($manifest) || ($manifest['complete'] ?? false) !== false) {
            throw new RuntimeException('The uploaded package session is invalid.');
        }

        $actualSize = File::size($partPath);
        $expectedSize = (int) ($manifest['total_size'] ?? 0);
        $expectedChunks = (int) ($manifest['total_chunks'] ?? 0);
        $nextChunk = (int) ($manifest['next_chunk'] ?? 0);

        if ($expectedSize < 1 || $expectedChunks < 1 || $nextChunk !== $expectedChunks || $actualSize !== $expectedSize) {
            throw new RuntimeException('The uploaded package is incomplete. Please upload it again.');
        }

        $this->assertSize($actualSize);

        $originalName = $this->safeFileName((string) ($manifest['file_name'] ?? 'package.bin'));
        $relativePath = $this->relativePath($project, $metadata, $originalName);
        $this->disk()->makeDirectory(dirname($relativePath));
        $targetPath = $this->disk()->path($relativePath);

        if (File::exists($targetPath)) {
            File::delete($targetPath);
        }

        if (!@rename($partPath, $targetPath)) {
            if (!@copy($partPath, $targetPath)) {
                throw new RuntimeException('The uploaded package could not be moved into release storage.');
            }
            File::delete($partPath);
        }

        $sha256 = hash_file('sha256', $targetPath);
        File::delete($manifestPath);

        return [
            'file_path' => $relativePath,
            'file_name' => $originalName,
            'file_size' => $actualSize,
            'sha256' => $sha256,
        ];
    }

    public function storeMarketplaceUploadedFile(UploadedFile $file, MarketplaceItem $item, array $metadata): array
    {
        $this->assertSize($file->getSize());

        $originalName = $this->safeFileName($file->getClientOriginalName());
        $relativePath = $this->marketplaceRelativePath($item, $metadata, $originalName);
        $this->disk()->makeDirectory(dirname($relativePath));

        $stored = $file->storeAs(dirname($relativePath), basename($relativePath), 'releases');
        if (!$stored) {
            throw new RuntimeException('The marketplace package could not be stored.');
        }

        return $this->metadata($stored, $originalName);
    }

    public function consumeUploadTokenToMarketplace(string $token, MarketplaceItem $item, array $metadata): array
    {
        $token = $this->safeToken($token);
        $manifestPath = $this->manifestPath($token);
        $partPath = $this->partPath($token);

        if (!File::exists($manifestPath) || !File::exists($partPath)) {
            throw new RuntimeException('The uploaded package session has expired or no longer exists.');
        }

        $manifest = json_decode((string) File::get($manifestPath), true);
        if (!is_array($manifest) || ($manifest['complete'] ?? false) !== true) {
            throw new RuntimeException('The uploaded marketplace package is incomplete.');
        }

        $actualSize = File::size($partPath);
        $expectedSize = (int) ($manifest['total_size'] ?? 0);
        $expectedChunks = (int) ($manifest['total_chunks'] ?? 0);
        $nextChunk = (int) ($manifest['next_chunk'] ?? 0);

        if ($expectedSize < 1 || $expectedChunks < 1 || $nextChunk !== $expectedChunks || $actualSize !== $expectedSize) {
            throw new RuntimeException('The uploaded marketplace package is incomplete.');
        }

        $this->assertSize($actualSize);

        $originalName = $this->safeFileName((string) ($manifest['file_name'] ?? 'package.bin'));
        $relativePath = $this->marketplaceRelativePath($item, $metadata, $originalName);
        $this->disk()->makeDirectory(dirname($relativePath));
        $targetPath = $this->disk()->path($relativePath);

        if (File::exists($targetPath)) {
            File::delete($targetPath);
        }

        if (!@rename($partPath, $targetPath)) {
            if (!@copy($partPath, $targetPath)) {
                throw new RuntimeException('The marketplace package could not be moved into release storage.');
            }
            File::delete($partPath);
        }

        $sha256 = hash_file('sha256', $targetPath);
        File::delete($manifestPath);

        return [
            'file_path' => $relativePath,
            'file_name' => $originalName,
            'file_size' => $actualSize,
            'sha256' => $sha256,
        ];
    }

    private function marketplaceRelativePath(MarketplaceItem $item, array $metadata, string $fileName): string
    {
        $projectSlug = Str::slug($item->project?->slug ?: 'project') ?: 'project';
        $itemSlug = Str::slug($item->slug ?: $item->item_id) ?: 'item';
        $version = Str::slug((string) ($metadata['version'] ?? 'unknown')) ?: 'unknown';
        $platform = Str::slug((string) ($metadata['platform'] ?? 'All')) ?: 'all';
        $architecture = Str::slug((string) ($metadata['architecture'] ?? 'All')) ?: 'all';
        $channel = Str::slug((string) ($metadata['channel'] ?? 'Stable')) ?: 'stable';

        return implode('/', [
            'marketplace',
            $projectSlug,
            $item->item_type,
            $itemSlug,
            $version,
            $platform,
            $architecture,
            $channel,
            $fileName,
        ]);
    }

    public function delete(?string $relativePath): void
    {
        if ($relativePath && $this->disk()->exists($relativePath)) {
            $this->disk()->delete($relativePath);
        }
    }

    public function cleanupUpload(string $token): void
    {
        $token = $this->safeToken($token);
        foreach ([$this->manifestPath($token), $this->partPath($token)] as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    public function startUpload(string $token, string $fileName, int $totalSize, int $totalChunks): array
    {
        $token = $this->safeToken($token);
        $fileName = $this->safeFileName($fileName);
        $this->assertSize($totalSize);

        if ($totalChunks < 1 || $totalChunks > 5000) {
            throw new RuntimeException('Invalid upload chunk count.');
        }

        $dir = $this->tempDirectory();
        File::ensureDirectoryExists($dir);
        $manifestPath = $this->manifestPath($token);
        $partPath = $this->partPath($token);

        File::put($manifestPath, json_encode([
            'file_name' => $fileName,
            'total_size' => $totalSize,
            'total_chunks' => $totalChunks,
            'next_chunk' => 0,
            'complete' => false,
            'created_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        if (File::exists($partPath)) {
            File::delete($partPath);
        }

        return ['next_chunk' => 0];
    }

    public function appendChunk(string $token, int $chunkIndex, int $totalChunks, UploadedFile $chunk): array
    {
        $token = $this->safeToken($token);
        $manifestPath = $this->manifestPath($token);
        $partPath = $this->partPath($token);

        if (!File::exists($manifestPath)) {
            throw new RuntimeException('Upload session was not initialized.');
        }

        $manifest = json_decode((string) File::get($manifestPath), true);
        if (!is_array($manifest)) {
            throw new RuntimeException('Upload session is invalid.');
        }

        if ((int) $manifest['total_chunks'] !== $totalChunks) {
            throw new RuntimeException('Upload chunk count does not match the initialized upload.');
        }

        $nextChunk = (int) $manifest['next_chunk'];
        if ($chunkIndex < $nextChunk) {
            return [
                'next_chunk' => $nextChunk,
                'complete' => (bool) $manifest['complete'],
                'received_bytes' => File::exists($partPath) ? File::size($partPath) : 0,
                'total_bytes' => (int) $manifest['total_size'],
                'alreadyReceived' => true,
            ];
        }
        if ($chunkIndex > $nextChunk) {
            throw new RuntimeException('Upload chunks must arrive in order.');
        }

        $chunkSize = (int) $chunk->getSize();
        if ($chunkSize < 1 || $chunkSize > 6_500_000) {
            throw new RuntimeException('Each upload chunk must be between 1 byte and 6.5 MB.');
        }

        $in = fopen($chunk->getRealPath(), 'rb');
        $out = fopen($partPath, $chunkIndex === 0 ? 'wb' : 'ab');
        if (!$in || !$out) {
            if (is_resource($in)) fclose($in);
            if (is_resource($out)) fclose($out);
            throw new RuntimeException('Unable to write the upload chunk.');
        }

        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        $manifest['next_chunk'] = $chunkIndex + 1;
        $manifest['complete'] = $manifest['next_chunk'] === $manifest['total_chunks'];
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        $currentSize = File::size($partPath);
        if ($currentSize > (int) $manifest['total_size']) {
            $this->cleanupUpload($token);
            throw new RuntimeException('The upload exceeded the declared file size.');
        }

        return [
            'next_chunk' => $manifest['next_chunk'],
            'complete' => $manifest['complete'],
            'received_bytes' => $currentSize,
            'total_bytes' => (int) $manifest['total_size'],
        ];
    }

    public function purgeStaleUploads(int $hours = 24): int
    {
        $deleted = 0;
        $dir = $this->tempDirectory();
        if (!File::isDirectory($dir)) {
            return 0;
        }

        foreach (File::files($dir) as $file) {
            if ($file->getMTime() < now()->subHours($hours)->getTimestamp()) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }

    private function metadata(string $relativePath, string $originalName): array
    {
        $absolute = $this->disk()->path($relativePath);
        return [
            'file_path' => $relativePath,
            'file_name' => $originalName,
            'file_size' => File::size($absolute),
            'sha256' => hash_file('sha256', $absolute),
        ];
    }

    private function relativePath(SoftwareProject $project, array $metadata, string $fileName): string
    {
        $projectSlug = Str::slug($project->slug ?: $project->name);
        $version = Str::slug((string) ($metadata['version'] ?? 'unknown')) ?: 'unknown';
        $platform = Str::slug((string) ($metadata['platform'] ?? 'unknown')) ?: 'unknown';
        $architecture = Str::slug((string) ($metadata['architecture'] ?? 'unknown')) ?: 'unknown';
        $channel = Str::slug((string) ($metadata['channel'] ?? 'stable')) ?: 'stable';

        return implode('/', [$projectSlug, $version, $platform, $architecture, $channel, $fileName]);
    }

    private function safeFileName(string $fileName): string
    {
        $fileName = basename(str_replace('\\', '/', $fileName));
        $fileName = preg_replace('/[^A-Za-z0-9._()\- ]+/', '-', $fileName) ?: 'package.bin';
        return trim($fileName, '. ' ) ?: 'package.bin';
    }

    private function safeToken(string $token): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]{20,100}$/', $token)) {
            throw new RuntimeException('Invalid upload token.');
        }
        return $token;
    }

    private function tempDirectory(): string
    {
        return (string) config('filesystems.release_upload_temp_path');
    }

    private function manifestPath(string $token): string
    {
        return $this->tempDirectory().DIRECTORY_SEPARATOR.$token.'.json';
    }

    private function partPath(string $token): string
    {
        return $this->tempDirectory().DIRECTORY_SEPARATOR.$token.'.part';
    }

    private function assertSize(?int $size): void
    {
        if ($size === null || $size < 1 || $size > self::MAX_BYTES) {
            throw new RuntimeException('Release package is empty or exceeds the 8 GiB safety limit.');
        }
    }
}
