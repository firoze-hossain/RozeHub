<?php

namespace App\Services;

use App\Models\Release;
use App\Models\SoftwareProject;
use App\Models\MarketplaceItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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
        $manifest = $this->readUploadManifest($token);
        $this->assertUploadOwner($manifest);
        $originalName = $this->safeFileName((string) ($manifest['file_name'] ?? 'package.bin'));
        $actualSize = $this->uploadedChunkSize($token, $manifest);
        $this->assertSize($actualSize);

        $relativePath = $this->relativePath($project, $metadata, $originalName);
        $this->finalizeChunkedUpload($token, $manifest, $relativePath);
        return $this->finalizedMetadata($relativePath, $originalName, $actualSize);
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
        $manifest = $this->readUploadManifest($token);
        $this->assertUploadOwner($manifest);
        $originalName = $this->safeFileName((string) ($manifest['file_name'] ?? 'package.bin'));
        $actualSize = $this->uploadedChunkSize($token, $manifest);
        $this->assertSize($actualSize);

        $relativePath = $this->marketplaceRelativePath($item, $metadata, $originalName);
        $this->finalizeChunkedUpload($token, $manifest, $relativePath);
        return $this->finalizedMetadata($relativePath, $originalName, $actualSize);
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
        $manifestPath = $this->manifestPath($token);
        $chunkDir = $this->chunkDirectory($token);

        if (File::exists($manifestPath)) {
            File::delete($manifestPath);
        }
        if (File::isDirectory($chunkDir)) {
            File::deleteDirectory($chunkDir);
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
        $this->cleanupUpload($token);
        File::ensureDirectoryExists($this->chunkDirectory($token));

        File::put($this->manifestPath($token), json_encode([
            'file_name' => $fileName,
            'total_size' => $totalSize,
            'total_chunks' => $totalChunks,
            'complete' => false,
            'created_at' => now()->toIso8601String(),
            'owner_user_id' => Auth::id(),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return ['next_chunk' => 0, 'total_chunks' => $totalChunks];
    }

    public function appendChunk(string $token, int $chunkIndex, int $totalChunks, UploadedFile $chunk): array
    {
        $token = $this->safeToken($token);
        $manifest = $this->readUploadManifest($token);
        $this->assertUploadOwner($manifest);

        if ((int) ($manifest['total_chunks'] ?? 0) !== $totalChunks) {
            throw new RuntimeException('Upload chunk count does not match the initialized upload.');
        }
        if ($chunkIndex < 0 || $chunkIndex >= $totalChunks) {
            throw new RuntimeException('Invalid upload chunk index.');
        }

        $chunkSize = (int) $chunk->getSize();
        // Deliberately below the default PHP 2 MB upload_max_filesize.
        if ($chunkSize < 1 || $chunkSize > 1_900_000) {
            throw new RuntimeException('Each upload chunk must be smaller than 1.9 MB.');
        }

        $expected = $this->expectedChunkSize($manifest, $chunkIndex);
        if ($chunkSize !== $expected) {
            throw new RuntimeException('The uploaded chunk size is invalid. Please retry this chunk.');
        }

        $chunkDir = $this->chunkDirectory($token);
        File::ensureDirectoryExists($chunkDir);
        $chunkPath = $this->chunkPath($token, $chunkIndex);

        if (File::exists($chunkPath) && File::size($chunkPath) === $chunkSize) {
            return [
                'chunk_index' => $chunkIndex,
                'received_bytes' => $this->uploadedChunkSize($token, $manifest),
                'total_bytes' => (int) $manifest['total_size'],
                'alreadyReceived' => true,
            ];
        }

        // Each chunk gets its own file. This makes parallel uploads safe: there is no
        // shared append stream and no manifest race between concurrent HTTP requests.
        $tmpPath = $chunkPath . '.tmp-' . Str::random(8);
        if (!@copy($chunk->getRealPath(), $tmpPath)) {
            throw new RuntimeException('Unable to write the upload chunk.');
        }
        if (File::size($tmpPath) !== $chunkSize) {
            File::delete($tmpPath);
            throw new RuntimeException('The uploaded chunk was truncated. Please retry.');
        }
        if (File::exists($chunkPath)) {
            File::delete($chunkPath);
        }
        if (!@rename($tmpPath, $chunkPath)) {
            File::delete($tmpPath);
            throw new RuntimeException('Unable to finalize the upload chunk.');
        }

        $received = $this->uploadedChunkSize($token, $manifest);
        $complete = $received === (int) $manifest['total_size'] && $this->allChunksPresent($token, $manifest);

        return [
            'chunk_index' => $chunkIndex,
            'received_bytes' => $received,
            'total_bytes' => (int) $manifest['total_size'],
            'complete' => $complete,
        ];
    }

    public function purgeStaleUploads(int $hours = 24): int
    {
        $deleted = 0;
        $dir = $this->tempDirectory();
        if (!File::isDirectory($dir)) return 0;
        $cutoff = now()->subHours($hours)->getTimestamp();

        foreach (File::files($dir) as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }
        foreach (File::directories($dir) as $directory) {
            if (File::lastModified($directory) < $cutoff) {
                File::deleteDirectory($directory);
                $deleted++;
            }
        }
        return $deleted;
    }

    private function readUploadManifest(string $token): array
    {
        $token = $this->safeToken($token);
        $path = $this->manifestPath($token);
        if (!File::exists($path)) {
            throw new RuntimeException('Upload session was not initialized or has expired.');
        }
        $manifest = json_decode((string) File::get($path), true);
        if (!is_array($manifest)) throw new RuntimeException('Upload session is invalid.');
        return $manifest;
    }

    private function assertUploadOwner(array $manifest): void
    {
        if (isset($manifest['owner_user_id']) && Auth::id() !== null && (int) $manifest['owner_user_id'] !== (int) Auth::id()) {
            throw new RuntimeException('This upload session belongs to another account.');
        }
    }

    private function expectedChunkSize(array $manifest, int $index): int
    {
        $total = (int) ($manifest['total_size'] ?? 0);
        $chunks = (int) ($manifest['total_chunks'] ?? 0);
        $max = 1_835_008; // 1.75 MiB, matching the browser uploader.
        if ($index < 0 || $index >= $chunks || $total < 1) {
            throw new RuntimeException('Invalid upload chunk.');
        }
        $start = $index * $max;
        return min($max, $total - $start);
    }

    private function uploadedChunkSize(string $token, array $manifest): int
    {
        $total = 0;
        $chunks = (int) ($manifest['total_chunks'] ?? 0);
        for ($i = 0; $i < $chunks; $i++) {
            $path = $this->chunkPath($token, $i);
            if (File::exists($path)) $total += File::size($path);
        }
        return $total;
    }

    private function allChunksPresent(string $token, array $manifest): bool
    {
        $chunks = (int) ($manifest['total_chunks'] ?? 0);
        for ($i = 0; $i < $chunks; $i++) {
            if (!File::exists($this->chunkPath($token, $i))) return false;
        }
        return true;
    }

    private function finalizeChunkedUpload(string $token, array $manifest, string $relativePath): void
    {
        $expectedSize = (int) ($manifest['total_size'] ?? 0);
        $expectedChunks = (int) ($manifest['total_chunks'] ?? 0);
        if ($expectedSize < 1 || $expectedChunks < 1 || !$this->allChunksPresent($token, $manifest)) {
            throw new RuntimeException('The uploaded package is incomplete. Please upload it again.');
        }
        $actualSize = $this->uploadedChunkSize($token, $manifest);
        if ($actualSize !== $expectedSize) {
            throw new RuntimeException('The uploaded package size does not match the original file.');
        }

        $this->disk()->makeDirectory(dirname($relativePath));
        $targetPath = $this->disk()->path($relativePath);
        if (File::exists($targetPath)) File::delete($targetPath);

        $out = @fopen($targetPath, 'wb');
        if (!$out) throw new RuntimeException('The package destination could not be opened.');
        try {
            for ($i = 0; $i < $expectedChunks; $i++) {
                $chunkPath = $this->chunkPath($token, $i);
                $in = @fopen($chunkPath, 'rb');
                if (!$in) throw new RuntimeException('A package chunk is missing during finalization.');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
        } finally {
            fclose($out);
        }

        if (File::size($targetPath) !== $expectedSize) {
            File::delete($targetPath);
            throw new RuntimeException('The final package size is invalid.');
        }

        $this->cleanupUpload($token);
    }

    private function metadata(string $relativePath, string $originalName): array
    {
        $absolute = $this->disk()->path($relativePath);

        if (!File::exists($absolute)) {
            throw new RuntimeException('The stored release package could not be found after upload.');
        }

        return [
            'file_path' => $relativePath,
            'file_name' => $originalName,
            'file_size' => File::size($absolute),
            'sha256' => hash_file('sha256', $absolute),
        ];
    }

    private function relativePath(SoftwareProject $project, array $metadata, string $fileName): string
    {
        $projectSlug = Str::slug((string) ($project->slug ?: $project->name)) ?: 'project';
        $version = Str::slug((string) ($metadata['version'] ?? 'unknown')) ?: 'unknown';
        $platform = Str::slug((string) ($metadata['platform'] ?? 'unknown')) ?: 'unknown';
        $architecture = Str::slug((string) ($metadata['architecture'] ?? 'unknown')) ?: 'unknown';
        $channel = Str::slug((string) ($metadata['channel'] ?? 'stable')) ?: 'stable';

        return implode('/', [
            $projectSlug,
            $version,
            $platform,
            $architecture,
            $channel,
            $fileName,
        ]);
    }

    /**
     * Keep only a filename. Never allow a client supplied filename to create
     * directories or traverse outside the configured release storage root.
     */
    private function safeFileName(string $fileName): string
    {
        $fileName = basename(str_replace('\\', '/', $fileName));
        $fileName = preg_replace('/[^A-Za-z0-9._()\- ]+/', '-', $fileName) ?: 'package.bin';
        $fileName = trim($fileName, '. ');

        return $fileName !== '' ? $fileName : 'package.bin';
    }

    private function finalizedMetadata(string $relativePath, string $originalName, int $actualSize): array
    {
        $absolute = $this->disk()->path($relativePath);
        return [
            'file_path' => $relativePath,
            'file_name' => $originalName,
            'file_size' => $actualSize,
            'sha256' => hash_file('sha256', $absolute),
        ];
    }

    private function tempDirectory(): string
    {
        return (string) config(
            'filesystems.release_upload_temp_path',
            dirname(base_path()).DIRECTORY_SEPARATOR.'rozehub-release-upload-temp'
        );
    }

    private function manifestPath(string $token): string
    {
        return $this->tempDirectory().DIRECTORY_SEPARATOR.$token.'.json';
    }

    private function chunkDirectory(string $token): string
    {
        return $this->tempDirectory().DIRECTORY_SEPARATOR.$token;
    }

    private function chunkPath(string $token, int $index): string
    {
        if ($index < 0 || $index > 4999) {
            throw new RuntimeException('Invalid upload chunk index.');
        }

        return $this->chunkDirectory($token).DIRECTORY_SEPARATOR.sprintf('%05d.part', $index);
    }

    private function safeToken(string $token): string
    {
        $token = trim($token);

        // Upload tokens are generated by Laravel's Str::random() and contain
        // only letters/numbers. Keep this strict because the token is used in
        // filesystem paths and must never be allowed to escape the temp folder.
        if (!preg_match('/^[A-Za-z0-9_-]{20,100}$/', $token)) {
            throw new RuntimeException('Invalid upload session token.');
        }

        return $token;
    }

    private function assertSize(?int $size): void
    {
        if ($size === null || $size < 1 || $size > self::MAX_BYTES) {
            throw new RuntimeException('Release package is empty or exceeds the 8 GiB safety limit.');
        }
    }
}
