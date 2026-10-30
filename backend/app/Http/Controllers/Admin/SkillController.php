<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;

class SkillController extends CrudController
{
    protected function model(): string { return Skill::class; }
    protected function meta(): array { return ['title' => 'Skills', 'singular' => 'Skill', 'route' => 'skills', 'icon' => 'bx-bulb']; }
    protected function searchable(): array { return ['name']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderBy('id'); }
    protected function columns(): array
    {
        return [['key' => 'name', 'label' => 'Skill', 'type' => 'title'], ['key' => 'level', 'label' => 'Level %'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Skill name', 'type' => 'text', 'col' => 6, 'rules' => 'required|max:80'],
            ['name' => 'level', 'label' => 'Level (0–100)', 'type' => 'number', 'col' => 3, 'default' => 80, 'rules' => 'required|integer|min:0|max:100'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'col' => 3, 'default' => 0, 'rules' => 'nullable|integer'],
        ];
    }
}
