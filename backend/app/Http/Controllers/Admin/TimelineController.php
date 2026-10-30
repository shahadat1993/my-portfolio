<?php

namespace App\Http\Controllers\Admin;

use App\Models\TimelineItem;

class TimelineController extends CrudController
{
    protected function model(): string { return TimelineItem::class; }
    protected function meta(): array { return ['title' => 'Journey timeline', 'singular' => 'Timeline item', 'route' => 'timeline', 'icon' => 'bx-git-branch']; }
    protected function searchable(): array { return ['label','title']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderBy('id'); }
    protected function columns(): array
    {
        return [['key' => 'label', 'label' => 'When'], ['key' => 'title', 'label' => 'Title', 'type' => 'title'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'label', 'label' => 'Year / label (2024, Today, Next)', 'type' => 'text', 'col' => 4, 'rules' => 'required|max:40'],
            ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'col' => 6, 'rules' => 'required|max:160'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'col' => 2, 'default' => 0, 'rules' => 'nullable|integer'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'col' => 12],
        ];
    }
}
