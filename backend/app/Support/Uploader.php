<?php
namespace App\Support;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Uploader
{
    /** Store an uploaded file on the public disk and register it in the media library. Returns relative path. */
    public static function store(UploadedFile $f, string $dir = 'uploads'): string
    {
        $mime = $f->getMimeType() ?: ($f->getClientMimeType() ?: '');
        $size = $f->getSize() ?: 0;
        $type = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'video/') ? 'video' : 'file');
        $w = $h = null;
        if ($type === 'image' && ($i = @getimagesize($f->getRealPath()))) { $w = $i[0]; $h = $i[1]; }
        $path = $f->store(trim($dir, '/') . '/' . date('Y/m'), 'public');
        Media::create(['path' => $path, 'type' => $type, 'title' => pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME), 'mime' => $mime, 'size' => $size, 'width' => $w, 'height' => $h]);
        return $path;
    }

    public static function delete(?string $path): void
    {
        if (!$path || preg_match('#^(https?:)?//#', $path)) return;
        Storage::disk('public')->delete($path);
        Media::where('path', $path)->delete();
    }
}
