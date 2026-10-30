<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactAutoReply;
use App\Mail\ContactReceived;
use App\Models\{Category, ContactMessage, Faq, Post, Project, Service, Setting, Skill, Subscriber, ThemeColor, TimelineItem};
use App\Support\GoogleSheet;
use App\Support\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    /** Everything the React shell needs on first load. */
    public function bootstrap()
    {
        $settings = Setting::publicMap();
        // CORS-enabled copies of photos (the hero portrait is read into a <canvas>, which needs CORS headers)
        foreach (['hero_photo', 'about_photo'] as $k) {
            $settings[$k . '_cors'] = ($p = Setting::get($k)) ? url('/api/image/' . ltrim($p, '/')) : null;
        }
        return response()->json([
            'settings' => $settings,
            'colors' => ThemeColor::orderBy('sort_order')->orderBy('id')->get(['name', 'hex', 'is_default']),
            'services' => Service::where('is_active', true)->orderBy('sort_order')->get()->map(fn ($s) => ['title' => $s->title, 'description' => $s->description, 'tags' => $s->tags ?? []]),
            'skills' => Skill::orderBy('sort_order')->get(['name', 'level']),
            'timeline' => TimelineItem::orderBy('sort_order')->get(['label', 'title', 'description']),
            'faqs' => Faq::orderBy('sort_order')->get(['scope', 'question', 'answer']),
            'categories' => [
                'project' => Category::where('type', 'project')->orderBy('sort_order')->get(['name', 'slug']),
                'blog' => Category::where('type', 'blog')->orderBy('sort_order')->get(['name', 'slug']),
            ],
        ]);
    }

    /** Serves an uploaded image through the API so the browser may read its pixels (CORS). */
    public function image(string $path)
    {
        abort_if(str_contains($path, '..') || !preg_match('/\.(jpe?g|png|webp|gif)$/i', $path), 404);
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);
        return $disk->response($path, null, ['Cache-Control' => 'public, max-age=86400']);
    }

    public function projects()
    {
        return response()->json(['data' => Project::published()->with('category')->ordered()->get()->map->toApi()->values()]);
    }

    public function project(string $slug)
    {
        $p = Project::published()->with('category')->where('slug', $slug)->firstOrFail();
        $all = Project::published()->ordered()->get(['id', 'slug', 'title']);
        $i = $all->search(fn ($x) => $x->id === $p->id);
        $next = $all[($i + 1) % $all->count()];
        return response()->json(['data' => $p->toApi(true) + ['next' => ['slug' => $next->slug, 'title' => $next->title]]]);
    }

    public function posts()
    {
        return response()->json(['data' => Post::published()->with('category')->orderByDesc('published_at')->orderByDesc('id')->get()->map->toApi()->values()]);
    }

    public function post(string $slug)
    {
        $p = Post::published()->with('category')->where('slug', $slug)->firstOrFail();
        $p->increment('views');
        $related = Post::published()->with('category')->where('id', '!=', $p->id)
            ->orderByRaw('category_id = ? desc', [$p->category_id ?? 0])->orderByDesc('published_at')->limit(3)->get()->map->toApi()->values();
        return response()->json(['data' => $p->toApi(true) + ['related' => $related]]);
    }

    public function search(Request $r)
    {
        return response()->json(['results' => SearchService::public((string) $r->query('q', ''))]);
    }

    public function contact(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'message' => 'required|string|min:10|max:5000',
            'project_type' => 'nullable|string|max:80',
            'budget' => 'nullable|string|max:80',
        ]);
        if ($r->filled('website')) return response()->json(['ok' => true]); // honeypot: bots fill this hidden field

        $m = ContactMessage::create($data + ['ip' => $r->ip(), 'user_agent' => substr((string) $r->userAgent(), 0, 250)]);

        // 1) email to you
        try {
            $to = array_values(array_filter(array_map('trim', explode(',', (string) Setting::get('contact_email', config('mail.from.address'))))));
            Mail::to($to)->send(new ContactReceived($m));
            $m->mail_sent = true;
        } catch (\Throwable $e) { Log::warning('Contact mail failed: ' . $e->getMessage()); }

        // 2) Google Sheet row
        $m->sheet_synced = GoogleSheet::push($m);
        $m->save();

        // 3) optional auto-reply to the visitor
        if (Setting::get('auto_reply') === '1') {
            try { Mail::to($m->email)->send(new ContactAutoReply($m)); } catch (\Throwable $e) { Log::warning('Auto-reply failed: ' . $e->getMessage()); }
        }

        return response()->json(['ok' => true, 'message' => 'Thanks! Your message has been sent.']);
    }

    public function subscribe(Request $r)
    {
        $r->validate(['email' => 'required|email|max:160']);
        Subscriber::firstOrCreate(['email' => strtolower($r->input('email'))], ['ip' => $r->ip()]);
        return response()->json(['ok' => true, 'message' => 'You are subscribed.']);
    }
}
