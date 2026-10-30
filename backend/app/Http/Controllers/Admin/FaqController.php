<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;

class FaqController extends CrudController
{
    protected function model(): string { return Faq::class; }
    protected function meta(): array { return ['title' => 'FAQs', 'singular' => 'FAQ', 'route' => 'faqs', 'icon' => 'bx-help-circle']; }
    protected function searchable(): array { return ['question','answer']; }
    protected function order($q) { return $q->orderBy('sort_order')->orderBy('id'); }
    protected function columns(): array
    {
        return [['key' => 'question', 'label' => 'Question', 'type' => 'title'], ['key' => 'scope', 'label' => 'Page', 'type' => 'badge'], ['key' => 'sort_order', 'label' => 'Order']];
    }
    protected function fields(): array
    {
        return [
            ['name' => 'scope', 'label' => 'Show on', 'type' => 'select', 'options' => ['about' => 'About page', 'contact' => 'Contact page'], 'col' => 4, 'rules' => 'required|in:about,contact'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'col' => 2, 'default' => 0, 'rules' => 'nullable|integer'],
            ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'col' => 12, 'rules' => 'required|max:255'],
            ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 5, 'col' => 12, 'rules' => 'required'],
        ];
    }
}
