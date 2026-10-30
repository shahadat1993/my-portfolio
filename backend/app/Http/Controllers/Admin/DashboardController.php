<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ContactMessage, Media, Post, Project, Setting, Subscriber};
use App\Support\GoogleSheet;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // messages per month, last 6 months (DB-agnostic bucketing)
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->startOfMonth()->subMonths($i));
        $rows = ContactMessage::where('created_at', '>=', $months->first())->pluck('created_at');
        $chart = ['labels' => $months->map->format('M')->all(), 'data' => $months->map(fn ($m) => $rows->filter(fn ($d) => $d->format('Y-m') === $m->format('Y-m'))->count())->all()];

        return view('admin.dashboard', [
            'stats' => [
                ['Projects', Project::count(), 'bx-briefcase', 'primary', route('admin.projects.index'), Project::where('is_published', true)->count() . ' live'],
                ['Blog posts', Post::count(), 'bx-edit', 'info', route('admin.posts.index'), number_format((int) Post::sum('views')) . ' views'],
                ['New messages', ContactMessage::where('status', 'new')->count(), 'bx-envelope', 'warning', route('admin.messages.index'), ContactMessage::count() . ' total'],
                ['Subscribers', Subscriber::count(), 'bx-bell', 'success', route('admin.subscribers.index'), 'newsletter'],
            ],
            'chart' => $chart,
            'recent' => ContactMessage::latest()->limit(6)->get(),
            'topPosts' => Post::orderByDesc('views')->limit(5)->get(),
            'media' => Media::count(),
            'checklist' => [
                ['Hero portrait photo uploaded', (bool) Setting::get('hero_photo'), route('admin.settings') . '#hero'],
                ['Contact email recipient set', (bool) Setting::get('contact_email'), route('admin.settings') . '#contact'],
                ['Mail driver configured (MAIL_MAILER ≠ log)', config('mail.default') !== 'log', null],
                ['Google Sheet webhook connected', GoogleSheet::enabled(), route('admin.settings') . '#contact'],
                ['At least one published project', Project::where('is_published', true)->exists(), route('admin.projects.create')],
            ],
        ]);
    }
}
