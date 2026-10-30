<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

class CategoryController extends CrudController
{
    protected function model(): string { return Category::class; }
    protected function meta(): array { return ['title' => 'Categories', 'singular' => 'Category', 'route' => 'categories', 'icon' => 'bx-category']; }
    protected function searchable(): array { return ['name','slug']; }
    protected function order($q) { return $q->orderBy('type')->orderBy('sort_order'); }
    protected function columns(): array
    {
        return [['key' => 'name', 'label' => 'Name', 'type' => 'title', 'sub' => 'slug'], ['key' => 'type', 'label' => 'Type', 'type' => 'badge'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'type', 'label' => 'Used for', 'type' => 'select', 'options' => ['project' => 'Projects', 'blog' => 'Blog posts'], 'col' => 3, 'rules' => 'required|in:project,blog'],
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'col' => 5, 'rules' => 'required|max:80'],
            ['name' => 'slug', 'label' => 'Slug (auto if empty)', 'type' => 'text', 'col' => 2],
            ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'col' => 2, 'default' => 0, 'rules' => 'nullable|integer'],
        ];
    }
}
