<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;

class ServiceController extends CrudController
{
    protected function model(): string { return Service::class; }
    protected function meta(): array { return ['title' => 'Services', 'singular' => 'Service', 'route' => 'services', 'icon' => 'bx-layer']; }
    protected function searchable(): array { return ['title','description']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderBy('id'); }
    protected function columns(): array
    {
        return [['key' => 'title', 'label' => 'Service', 'type' => 'title'], ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Service title', 'type' => 'text', 'col' => 8, 'rules' => 'required|max:120'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'col' => 2, 'default' => 0, 'rules' => 'nullable|integer'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'toggle', 'col' => 2, 'default' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
            ['name' => 'tags', 'label' => 'Tech tags (comma separated)', 'type' => 'tags', 'col' => 12],
        ];
    }
}
