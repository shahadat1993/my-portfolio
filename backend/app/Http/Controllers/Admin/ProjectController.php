<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Category, Project};

class ProjectController extends CrudController
{
    protected function model(): string { return Project::class; }
    protected function meta(): array { return ['title' => 'Projects', 'singular' => 'Project', 'route' => 'projects', 'icon' => 'bx-briefcase']; }
    protected function searchable(): array { return ['title', 'summary', 'client']; }
    protected function with(): array { return ['category']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderByDesc('id'); }

    protected function columns(): array
    {
        return [
            ['key' => 'cover_url', 'label' => '', 'type' => 'image'],
            ['key' => 'title', 'label' => 'Project', 'type' => 'title', 'sub' => 'slug'],
            ['key' => 'category.name', 'label' => 'Category', 'type' => 'badge'],
            ['key' => 'year', 'label' => 'Year'],
            ['key' => 'is_featured', 'label' => 'Featured', 'type' => 'bool'],
            ['key' => 'is_published', 'label' => 'Live', 'type' => 'bool'],
        ];
    }

    protected function fields(): array
    {
        $cats = Category::where('type', 'project')->orderBy('name')->pluck('name', 'id')->all();
        return [
            ['type' => 'section', 'label' => 'Basics', 'icon' => 'bx-info-circle'],
            ['name' => 'title', 'label' => 'Project title', 'type' => 'text', 'col' => 8, 'rules' => 'required|string|max:160'],
            ['name' => 'short_code', 'label' => 'Short code (2 letters)', 'type' => 'text', 'col' => 4, 'rules' => 'nullable|max:6'],
            ['name' => 'slug', 'label' => 'URL slug (auto if empty)', 'type' => 'text', 'col' => 4],
            ['name' => 'category_id', 'label' => 'Category', 'type' => 'select', 'options' => $cats, 'col' => 4, 'empty' => '— none —', 'rules' => 'nullable|exists:categories,id'],
            ['name' => 'status', 'label' => 'Status (Live, In progress…)', 'type' => 'text', 'col' => 4],
            ['name' => 'year', 'label' => 'Year', 'type' => 'text', 'col' => 3],
            ['name' => 'role', 'label' => 'Your role', 'type' => 'text', 'col' => 3],
            ['name' => 'client', 'label' => 'Client / company', 'type' => 'text', 'col' => 3],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'col' => 3, 'default' => 0, 'rules' => 'nullable|integer'],
            ['name' => 'is_featured', 'label' => 'Featured on home page', 'type' => 'toggle', 'col' => 4, 'default' => false],
            ['name' => 'is_published', 'label' => 'Published (visible on site)', 'type' => 'toggle', 'col' => 4, 'default' => true],
            ['type' => 'section', 'label' => 'Media', 'icon' => 'bx-images'],
            ['name' => 'cover_image', 'label' => 'Cover image', 'type' => 'image', 'col' => 6, 'help' => 'Shown in cards and the 3D ring (recommended 1200×750).'],
            ['name' => 'hero_video', 'label' => 'Demo video (upload mp4/webm, or paste YouTube/Vimeo/mp4 URL)', 'type' => 'video', 'col' => 6],
            ['name' => 'gallery', 'label' => 'Screenshots / gallery (add as many as you want)', 'type' => 'gallery', 'col' => 12],
            ['type' => 'section', 'label' => 'Links & tech', 'icon' => 'bx-link'],
            ['name' => 'live_url', 'label' => 'Live URL', 'type' => 'text', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            ['name' => 'repo_url', 'label' => 'Repository URL', 'type' => 'text', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            ['name' => 'tags', 'label' => 'Tech stack tags (comma separated)', 'type' => 'tags', 'col' => 12],
            ['type' => 'section', 'label' => 'Story (rich text — write as much as you need)', 'icon' => 'bx-book-content'],
            ['name' => 'summary', 'label' => 'Short summary (cards, search, SEO)', 'type' => 'textarea', 'rows' => 3, 'col' => 12],
            ['name' => 'overview', 'label' => 'Overview', 'type' => 'editor', 'col' => 12, 'height' => 380],
            ['name' => 'challenge', 'label' => 'Challenge', 'type' => 'editor', 'col' => 12, 'height' => 320],
            ['name' => 'solution', 'label' => 'Solution', 'type' => 'editor', 'col' => 12, 'height' => 320],
            ['name' => 'results', 'label' => 'Results (one per line)', 'type' => 'list', 'col' => 6, 'rows' => 6],
            ['name' => 'features', 'label' => 'Key features (one per line)', 'type' => 'list', 'col' => 6, 'rows' => 6],
            ['type' => 'section', 'label' => 'Full case study (unlimited length, images, video, tables, code)', 'icon' => 'bx-detail'],
            ['name' => 'content', 'label' => 'Detailed content', 'type' => 'editor', 'col' => 12, 'height' => 640],
            ['type' => 'section', 'label' => 'SEO', 'icon' => 'bx-search-alt'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text', 'col' => 6],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea', 'rows' => 2, 'col' => 6],
        ];
    }
}
