<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Uploader;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show()
    {
        return view('admin.settings.index', ['groups' => config('site.groups'), 'values' => Setting::map()]);
    }

    public function update(Request $r)
    {
        $r->validate(['hero_photo' => 'nullable|image|max:8192', 'about_photo' => 'nullable|image|max:8192']);
        $current = Setting::map();
        foreach (config('site.groups') as $g) {
            foreach ($g['fields'] as $f) {
                $k = $f['key'];
                if ($f['type'] === 'image') {
                    if ($r->hasFile($k)) { Uploader::delete($current[$k] ?? null); Setting::put($k, Uploader::store($r->file($k), 'site')); }
                    elseif ($r->boolean('remove_' . $k)) { Uploader::delete($current[$k] ?? null); Setting::put($k, null); }
                    continue;
                }
                if ($f['type'] === 'toggle') { Setting::put($k, $r->boolean($k) ? '1' : '0'); continue; }
                Setting::put($k, $r->input($k));
            }
        }
        Setting::forgetCache();
        return back()->with('ok', 'Settings saved.');
    }
}
