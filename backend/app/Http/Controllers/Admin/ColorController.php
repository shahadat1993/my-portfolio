<?php

namespace App\Http\Controllers\Admin;

use App\Models\ThemeColor;

class ColorController extends CrudController
{
    protected function model(): string { return ThemeColor::class; }
    protected function meta(): array { return ['title' => 'Theme colours', 'singular' => 'Colour', 'route' => 'colors', 'icon' => 'bx-palette']; }
    protected function searchable(): array { return ['name','hex']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderBy('id'); }
    protected function columns(): array
    {
        return [['key' => 'hex', 'label' => '', 'type' => 'color'], ['key' => 'name', 'label' => 'Name', 'type' => 'title'], ['key' => 'hex', 'label' => 'Hex'], ['key' => 'is_default', 'label' => 'Default', 'type' => 'bool'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Colour name', 'type' => 'text', 'col' => 5, 'rules' => 'required|max:60'],
            ['name' => 'hex', 'label' => 'Colour', 'type' => 'color', 'col' => 3, 'default' => '#ff6a3d', 'rules' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/']],
            ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'col' => 2, 'default' => 0, 'rules' => 'nullable|integer'],
            ['name' => 'is_default', 'label' => 'Default accent', 'type' => 'toggle', 'col' => 2, 'default' => false],
        ];
    }

    protected function afterSave($m): void
    {
        if ($m->is_default) ThemeColor::where('id', '!=', $m->id)->update(['is_default' => false]);
    }
}
