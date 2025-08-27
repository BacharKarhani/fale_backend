<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // ⬅️ جديد
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyLogoController extends Controller
{
    public function download(User $user): BinaryFileResponse
    {
        $path = $user->photo;
        if (!$path) {
            abort(404, 'Logo not found');
        }

        $relative = preg_replace('#^storage/#', '', ltrim($path, '/'));
        $disk     = Storage::disk('public');

        if (!$disk->exists($relative)) {
            abort(404, 'Logo missing on disk');
        }

        $absolutePath = $disk->path($relative);
        $filename     = basename($absolutePath);

        // ✅ استخدم File::mimeType بدل disk->mimeType لتفادي تحذير IDE
        $mime = File::mimeType($absolutePath) ?? 'application/octet-stream';

        return response()->download($absolutePath, $filename, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
        ]);
    }
}
