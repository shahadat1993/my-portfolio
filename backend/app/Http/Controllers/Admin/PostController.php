<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Category, Post};
use Illuminate\Support\Carbon;

class PostController extends CrudController
{
    protected function model(): string { return Post::class; }
    protected function meta(): array { return ['title' => 'Blog posts', 'singular' => 'Post', 'route' => 'posts', 'icon' => 'bx-edit']; }
    protected function searchable(): array { return ['title', 'excerpt']; }
    protected function with(): array { return ['category']; }
    protected function order($q) { return $q->orderByDesc('published_at')->orderByDesc('id'); }

    protected function columns(): array
    {
        return [
            ['key' => 'cover_url', 'label' => '', 'type' => 'image'],
            ['key' => 'title', 'label' => 'Post', 'type' => 'title', 'sub' => 'slug'],
            ['key' => 'category.name', 'label' => 'Category', 'type' => 'badge'],
            ['key' => 'published_at', 'label' => 'Date', 'type' => 'date'],
            ['key' => 'views', 'label' => 'Views'],
            ['key' => 'is_published', 'label' => 'Live', 'type' => 'bool'],
        ];
    }

    protected function fields(): array
    {
        $cats = Category::where('type', 'blog')->orderBy('name')->pluck('name', 'id')->all();
        return [
            ['type' => 'section', 'label' => 'Post details', 'icon' => 'bx-info-circle'],
            ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'col' => 12, 'rules' => 'required|string|max:200'],
            ['name' => 'slug', 'label' => 'URL slug (auto if empty)', 'type' => 'text', 'col' => 4],
            ['name' => 'category_id', 'label' => 'Category', 'type' => 'select', 'options' => $cats, 'col' => 4, 'empty' => '— none —', 'rules' => 'nullable|exists:categories,id'],
            ['name' => 'author_name', 'label' => 'Author', 'type' => 'text', 'col' => 4, 'default' => 'Shahadat'],
            ['name' => 'published_at', 'label' => 'Publish date', 'type' => 'datetime', 'col' => 4],
            ['name' => 'is_featured', 'label' => 'Featured (top of blog page)', 'type' => 'toggle', 'col' => 4, 'default' => false],
            ['name' => 'is_published', 'label' => 'Published', 'type' => 'toggle', 'col' => 4, 'default' => true],
            ['name' => 'excerpt', 'label' => 'Excerpt (cards, search results, SEO)', 'type' => 'textarea', 'rows' => 3, 'col' => 12],
            ['name' => 'cover_image', 'label' => 'Cover image', 'type' => 'image', 'col' => 6, 'help' => 'Shown on cards, the 3D ring and at the top of the article.'],
            ['name' => 'tags', 'label' => 'Tags (comma separated)', 'type' => 'tags', 'col' => 6],
            ['type' => 'section', 'label' => 'Article (rich text: add images at the start, middle or end — align left / right / centre)', 'icon' => 'bx-book-open'],
            ['name' => 'content', 'label' => 'Content', 'type' => 'editor', 'col' => 12, 'height' => 720],
            ['type' => 'section', 'label' => 'SEO', 'icon' => 'bx-search-alt'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text', 'col' => 6],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea', 'rows' => 2, 'col' => 6],
        ];
    }

    protected function beforeSave(array &$d, $m = null): void
    {
        $words = str_word_count(strip_tags((string) ($d['content'] ?? '')));
        $d['read_time'] = max(1, (int) ceil($words / 200));
        if (empty($d['published_at']) && !empty($d['is_published'])) $d['published_at'] = Carbon::now();
    }
}
