<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return ['tags' => 'array', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime'];
    }
    public function category() { return $this->belongsTo(Category::class); }
    public function scopePublished($q) { return $q->where('is_published', true)->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now())); }
    public function getCoverUrlAttribute(): ?string { return Media::url($this->cover_image); }

    public function toApi(bool $full = false): array
    {
        $a = [
            'id' => $this->id, 'slug' => $this->slug, 'title' => $this->title, 'excerpt' => $this->excerpt,
            'cover' => Media::url($this->cover_image), 'category' => $this->category?->slug, 'category_name' => $this->category?->name,
            'date' => ($this->published_at ?? $this->created_at)->format('M d, Y'), 'minutes' => $this->read_time,
            'tags' => $this->tags ?? [], 'featured' => $this->is_featured, 'views' => $this->views,
        ];
        if (!$full) return $a;
        return $a + ['content' => Media::fixHtml($this->content), 'author' => $this->author_name, 'seo_title' => $this->seo_title, 'seo_description' => $this->seo_description];
    }
}
