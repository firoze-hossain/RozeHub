<?php

namespace App\Http\Controllers;

use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class AdminReleaseUploadController extends Controller
{
    public function __construct(private readonly ReleaseStorageService $storage)
    {
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'file_name' => ['required', 'string', 'max:255'],
            'total_size' => ['required', 'integer', 'min:1', 'max:8589934592'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $token = Str::random(48);
        $this->storage->startUpload($token, $data['file_name'], (int) $data['total_size'], (int) $data['total_chunks']);

        return response()->json(['token' => $token, 'next_chunk' => 0]);
    }

    public function chunk(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{20,100}$/'],
            'chunk_index' => ['required', 'integer', 'min:0', 'max:4999'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:5000'],
            'chunk' => ['required', 'file', 'max:1900'],
        ]);

        return response()->json($this->storage->appendChunk(
            $data['token'],
            (int) $data['chunk_index'],
            (int) $data['total_chunks'],
            $request->file('chunk')
        ));
    }

    public function cancel(string $token)
    {
        try {
            $this->storage->cleanupUpload($token);
        } catch (Throwable) {
            // Cleanup is intentionally idempotent.
        }

        return response()->json(['ok' => true]);
    }
}
