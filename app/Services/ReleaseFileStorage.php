<?php

namespace App\Services;

use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReleaseFileStorage
{
    public function root(): string
    {
        return rtrim((string) config('release_storage.root'), DIRECTORY_SEPARATOR);
    }

    /**
     * Store a release package in:
     *   <project-slug>/<version>/<original-file-name>
     *
     * Only the relative path is stored in the database.
     */
    public function store(UploadedFile $file, SoftwareProject $project, string $version): array
    {
        $projectDirectory = $this->safeSegment($project->slug ?: $project->name);
        $versionDirectory = $this->safeSegment($version);
        $directory = $this->root() . DIRECTORY_SEPARATOR . $projectDirectory . DIRECTORY_SEPARATOR . $versionDirectory;

        File::ensureDirectoryExists($directory);

        $fileName = $this->safeFileName($file->getClientOriginalName());
        $destination = $directory . DIRECTORY_SEPARATOR . $fileName;

        // Never overwrite an unrelated file with the same name.
        if (File::exists($destination)) {
            $fileName = $this->uniqueFileName($directory, $fileName);
            $destination = $directory . DIRECTORY_SEPARATOR . $fileName;
        }

        if (! $file->move($directory, $fileName)) {
            throw new RuntimeException('Unable to move the release package into release storage.');
        }

        return [
            'file_path' => $projectDirectory . '/' . $versionDirectory . '/' . $fileName,
            'file_name' => $fileName,
            'file_size' => File::size($destination),
            'sha256' => hash_file('sha256', $destination),
        ];
    }


    /**
     * Move an existing package when its project or version changes.
     * The database continues to store only the new relative path.
     */
    public function relocate(?string $path, SoftwareProject $project, string $version): ?array
    {
        $source = $this->absolutePath($path);
        if (! $source) {
            return null;
        }

        $projectDirectory = $this->safeSegment($project->slug ?: $project->name);
        $versionDirectory = $this->safeSegment($version);
        $directory = $this->root() . DIRECTORY_SEPARATOR . $projectDirectory . DIRECTORY_SEPARATOR . $versionDirectory;
        File::ensureDirectoryExists($directory);

        $fileName = $this->safeFileName(basename($source));
        $destination = $directory . DIRECTORY_SEPARATOR . $fileName;

        // If the file is already in the correct location, only refresh metadata.
        if (realpath($source) !== false && realpath($destination) === realpath($source)) {
            return [
                'file_path' => $projectDirectory . '/' . $versionDirectory . '/' . $fileName,
                'file_name' => $fileName,
                'file_size' => File::size($source),
                'sha256' => hash_file('sha256', $source),
            ];
        }

        if (File::exists($destination)) {
            $fileName = $this->uniqueFileName($directory, $fileName);
            $destination = $directory . DIRECTORY_SEPARATOR . $fileName;
        }

        if (! File::copy($source, $destination)) {
            throw new RuntimeException('Unable to relocate the existing release package.');
        }

        // Delete the old file and clean its empty version/project folders.
        if ($this->isInsideRoot($source)) {
            File::delete($source);
            $this->removeEmptyParents(dirname($source));
        } else {
            File::delete($source);
            $this->removeEmptyParents(dirname($source), storage_path('app/private'));
        }

        return [
            'file_path' => $projectDirectory . '/' . $versionDirectory . '/' . $fileName,
            'file_name' => $fileName,
            'file_size' => File::size($destination),
            'sha256' => hash_file('sha256', $destination),
        ];
    }

    /**
     * Delete a release package and clean empty version/project directories.
     * Also understands the old Laravel-local storage path used by previous versions.
     */
    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalized = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $absolute = $this->root() . DIRECTORY_SEPARATOR . $normalized;

        if ($this->isInsideRoot($absolute) && File::exists($absolute) && File::isFile($absolute)) {
            File::delete($absolute);
            $this->removeEmptyParents(dirname($absolute));
            return;
        }

        // Backward compatibility for packages uploaded by older RozeHub versions.
        $legacy = storage_path('app/private/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, ltrim($path, '/\\')));
        if (File::exists($legacy) && File::isFile($legacy)) {
            File::delete($legacy);
            $this->removeEmptyParents(dirname($legacy), storage_path('app/private'));
        }
    }

    /**
     * Return the absolute path for a release, including legacy files.
     */
    public function absolutePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $absolute = $this->root() . DIRECTORY_SEPARATOR . $normalized;

        if ($this->isInsideRoot($absolute) && File::exists($absolute) && File::isFile($absolute)) {
            return $absolute;
        }

        $legacy = storage_path('app/private/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, ltrim($path, '/\\')));
        if (File::exists($legacy) && File::isFile($legacy)) {
            return $legacy;
        }

        return null;
    }

    private function removeEmptyParents(string $directory, ?string $stopAt = null): void
    {
        $stopAt = $stopAt ? rtrim(realpath($stopAt) ?: $stopAt, DIRECTORY_SEPARATOR) : rtrim($this->root(), DIRECTORY_SEPARATOR);
        $current = $directory;

        while ($current && $current !== $stopAt && $this->isInsideRoot($current, $stopAt)) {
            if (! File::isDirectory($current) || count(File::allFiles($current)) > 0 || count(File::directories($current)) > 0) {
                break;
            }

            @File::deleteDirectory($current);
            $parent = dirname($current);
            if ($parent === $current) {
                break;
            }
            $current = $parent;
        }
    }

    private function isInsideRoot(string $path, ?string $root = null): bool
    {
        $root = rtrim($root ?: $this->root(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $candidate = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $rootReal = realpath($root);
        if ($rootReal !== false) {
            $root = rtrim($rootReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return str_starts_with($candidate, $root);
    }

    private function safeSegment(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^A-Za-z0-9._-]+/', '-', $value) ?: 'release';
        $value = trim($value, '.-_' );
        return $value !== '' && $value !== '.' && $value !== '..' ? $value : 'release';
    }

    private function safeFileName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[^A-Za-z0-9._()\[\] -]+/', '-', $name) ?: 'package';
        $name = trim($name, " .");
        return $name !== '' ? $name : 'package';
    }

    private function uniqueFileName(string $directory, string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $base = pathinfo($fileName, PATHINFO_FILENAME);
        $counter = 2;

        do {
            $candidate = $base . '-' . $counter . ($extension ? '.' . $extension : '');
            $counter++;
        } while (File::exists($directory . DIRECTORY_SEPARATOR . $candidate));

        return $candidate;
    }
}
