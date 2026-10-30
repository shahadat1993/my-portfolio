<?php
namespace App\Support;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Pushes a contact message to a Google Sheet through a Google Apps Script web-app webhook. */
class GoogleSheet
{
    public static function enabled(): bool { return (bool) Setting::get('sheet_webhook_url'); }

    public static function push(ContactMessage $m): bool
    {
        $url = Setting::get('sheet_webhook_url');
        if (!$url) return false;
        try {
            $r = Http::timeout(20)->asJson()->post($url, [
                'secret' => (string) Setting::get('sheet_secret', ''),
                'id' => $m->id, 'date' => $m->created_at->format('Y-m-d H:i:s'),
                'name' => $m->name, 'email' => $m->email, 'project_type' => $m->project_type, 'budget' => $m->budget,
                'message' => $m->message, 'status' => $m->status, 'ip' => $m->ip,
            ]);
            $ok = $r->successful() && $r->json('ok') === true;
            if (!$ok) Log::warning('Google Sheet webhook rejected: ' . substr($r->body(), 0, 300));
            return $ok;
        } catch (\Throwable $e) {
            Log::warning('Google Sheet webhook failed: ' . $e->getMessage());
            return false;
        }
    }
}
