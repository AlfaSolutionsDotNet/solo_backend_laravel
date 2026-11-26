<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class PublicUploadController extends Controller
{
    public function show(string $filename)
    {
        // Only direct children of uploads are public; never follow a path out
        // of the configured public disk, including through symlinks.
        abort_if($filename === '.' || $filename === '..' || strpos($filename, '/') !== false || strpos($filename, "\\") !== false, 404);
        $disk = Storage::disk('public');
        $root = realpath($disk->path('uploads'));
        $path = realpath($disk->path('uploads/' . $filename));
        abort_unless($root && $path && dirname($path) === $root && is_file($path), 404);

        return response()->file($path, [
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }
}
