<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $guarded = [];
    protected $appends = ['url'];

    public static function url(?string $path): ?string
    {
        if (!$path) return null;
        if (preg_match('#^(https?:)?//#', $path)) return $path;
        return url('storage/' . ltrim($path, '/'));
    }

    /** Rewrite /storage/... (relative or any host) inside rich-text HTML to the current API host. */
    public static function fixHtml(?string $html): ?string
    {
        if (!$html) return $html;
        $base = rtrim(url('/'), '/');
        return preg_replace('#(src|href|poster)=(["\'])(?:https?://[^/"\']+)?/storage/#i', '$1=$2' . $base . '/storage/', $html);
    }

    public function getUrlAttribute(): ?string { return static::url($this->path); }
}
