<?php
namespace App\Support;

use App\Models\{Category, ContactMessage, Faq, Media, Post, Project, Service, Skill};
use Illuminate\Support\Str;

class SearchService
{
    protected static function like(string $q): string { return '%' . str_replace(['%', '_', '\\'], '', $q) . '%'; }

    protected static function score(string $title, string $q): int
    {
        $t = Str::lower($title); $q = Str::lower($q);
        return (str_starts_with($t, $q) ? 3 : 0) + (str_contains($t, $q) ? 2 : 0);
    }

    /** Public website live search → [{type,title,excerpt,url,image}] */
    public static function public(string $q, int $limit = 8): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 2) return [];
        $like = static::like($q);
        $out = [];
        Project::published()->with('category')->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('summary', 'like', $like)->orWhere('tags', 'like', $like)->orWhere('client', 'like', $like))->limit(8)->get()
            ->each(function ($p) use (&$out, $q) {
                $out[] = ['type' => 'project', 'title' => $p->title, 'excerpt' => Str::limit((string) $p->summary, 90), 'url' => '/projects/' . $p->slug, 'image' => Media::url($p->cover_image), '_s' => static::score($p->title, $q)];
            });
        Post::published()->with('category')->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('tags', 'like', $like))->limit(8)->get()
            ->each(function ($p) use (&$out, $q) {
                $out[] = ['type' => 'post', 'title' => $p->title, 'excerpt' => Str::limit((string) $p->excerpt, 90), 'url' => '/blogs/' . $p->slug, 'image' => Media::url($p->cover_image), '_s' => static::score($p->title, $q)];
            });
        usort($out, fn ($a, $b) => $b['_s'] <=> $a['_s']);
        return array_map(function ($r) { unset($r['_s']); return $r; }, array_slice($out, 0, $limit));
    }

    /** Admin dashboard live search → [{type,title,sub,url,image,icon}] */
    public static function admin(string $q, int $limit = 10): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 1) return [];
        $like = static::like($q);
        $out = [];
        Project::where('title', 'like', $like)->orWhere('summary', 'like', $like)->limit(5)->get()->each(function ($p) use (&$out) {
            $out[] = ['type' => 'Project', 'title' => $p->title, 'sub' => Str::limit((string) $p->summary, 60), 'url' => route('admin.projects.edit', $p), 'image' => Media::url($p->cover_image), 'icon' => 'bx-briefcase'];
        });
        Post::where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->limit(5)->get()->each(function ($p) use (&$out) {
            $out[] = ['type' => 'Post', 'title' => $p->title, 'sub' => Str::limit((string) $p->excerpt, 60), 'url' => route('admin.posts.edit', $p), 'image' => Media::url($p->cover_image), 'icon' => 'bx-edit'];
        });
        ContactMessage::where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('message', 'like', $like)->limit(4)->get()->each(function ($m) use (&$out) {
            $out[] = ['type' => 'Message', 'title' => $m->name . ' · ' . $m->email, 'sub' => Str::limit($m->message, 60), 'url' => route('admin.messages.show', $m), 'image' => null, 'icon' => 'bx-envelope'];
        });
        Category::where('name', 'like', $like)->limit(3)->get()->each(function ($c) use (&$out) {
            $out[] = ['type' => 'Category', 'title' => $c->name, 'sub' => ucfirst($c->type), 'url' => route('admin.categories.edit', $c), 'image' => null, 'icon' => 'bx-category'];
        });
        Skill::where('name', 'like', $like)->limit(2)->get()->each(function ($s) use (&$out) {
            $out[] = ['type' => 'Skill', 'title' => $s->name, 'sub' => $s->level . '%', 'url' => route('admin.skills.edit', $s), 'image' => null, 'icon' => 'bx-bulb'];
        });
        Service::where('title', 'like', $like)->limit(2)->get()->each(function ($s) use (&$out) {
            $out[] = ['type' => 'Service', 'title' => $s->title, 'sub' => Str::limit((string) $s->description, 60), 'url' => route('admin.services.edit', $s), 'image' => null, 'icon' => 'bx-layer'];
        });
        Faq::where('question', 'like', $like)->limit(2)->get()->each(function ($f) use (&$out) {
            $out[] = ['type' => 'FAQ', 'title' => $f->question, 'sub' => ucfirst($f->scope), 'url' => route('admin.faqs.edit', $f), 'image' => null, 'icon' => 'bx-help-circle'];
        });
        foreach ([['Site settings', 'admin.settings', 'bx-cog', 'settings email sheet seo social hero'], ['Theme colours', 'admin.colors.index', 'bx-palette', 'colors accent palette'], ['Media library', 'admin.media.index', 'bx-images', 'images videos upload'], ['Messages', 'admin.messages.index', 'bx-envelope', 'contact inbox export excel']] as [$t, $r, $i, $kw]) {
            if (str_contains(Str::lower($t . ' ' . $kw), Str::lower($q))) $out[] = ['type' => 'Page', 'title' => $t, 'sub' => 'Admin page', 'url' => route($r), 'image' => null, 'icon' => $i];
        }
        return array_slice($out, 0, $limit);
    }
}
