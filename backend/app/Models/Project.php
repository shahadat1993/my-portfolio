<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return ['tags' => 'array', 'results' => 'array', 'features' => 'array', 'gallery' => 'array',
                'is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime'];
    }
    public function category() { return $this->belongsTo(Category::class); }
    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderByDesc('id'); }
    public function getCoverUrlAttribute(): ?string { return Media::url($this->cover_image); }

    public function toApi(bool $full = false): array
    {
        $a = [
            'id' => $this->id, 'slug' => $this->slug, 'title' => $this->title,
            'code' => $this->short_code ?: Str::upper(Str::substr($this->title, 0, 2)),
            'category' => $this->category?->slug, 'category_name' => $this->category?->name,
            'summary' => $this->summary, 'cover' => Media::url($this->cover_image),
            'tags' => $this->tags ?? [], 'year' => $this->year, 'status' => $this->status, 'featured' => $this->is_featured,
        ];
        if (!$full) return $a;
        $video = $this->hero_video;
        $embed = $video && preg_match('#(youtube\.com|youtu\.be|vimeo\.com)#i', $video);
        return $a + [
            'role' => $this->role, 'client' => $this->client, 'live_url' => $this->live_url, 'repo_url' => $this->repo_url,
            'video' => $embed ? $this->embedUrl($video) : Media::url($video), 'video_kind' => $video ? ($embed ? 'embed' : 'file') : null,
            'overview' => Media::fixHtml($this->overview), 'challenge' => Media::fixHtml($this->challenge), 'solution' => Media::fixHtml($this->solution), 'content' => Media::fixHtml($this->content),
            'results' => $this->results ?? [], 'features' => $this->features ?? [],
            'gallery' => collect($this->gallery ?? [])->map(fn ($g) => ['src' => Media::url($g['src'] ?? null), 'caption' => $g['caption'] ?? ''])->filter(fn ($g) => $g['src'])->values()->all(),
            'seo_title' => $this->seo_title, 'seo_description' => $this->seo_description,
        ];
    }

    protected function embedUrl(string $u): string
    {
        if (preg_match('#youtu\.be/([\w-]+)#', $u, $m) || preg_match('#v=([\w-]+)#', $u, $m)) return 'https://www.youtube.com/embed/' . $m[1];
        if (preg_match('#vimeo\.com/(\d+)#', $u, $m)) return 'https://player.vimeo.com/video/' . $m[1];
        return $u;
    }
}
