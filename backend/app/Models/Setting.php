<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    /** key => value for every field in config/site.php (DB value or schema default). */
    public static function map(): array
    {
        return Cache::rememberForever('settings.map', function () {
            $db = static::query()->pluck('value', 'key')->all();
            $out = [];
            foreach (config('site.groups') as $g) {
                foreach ($g['fields'] as $f) {
                    $k = $f['key'];
                    $out[$k] = (array_key_exists($k, $db) && $db[$k] !== null) ? $db[$k] : ($f['default'] ?? null);
                }
            }
            return $out;
        });
    }

    public static function get(string $key, $default = null)
    {
        $v = static::map()[$key] ?? null;
        return ($v === null || $v === '') ? $default : $v;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::forgetCache();
    }

    public static function forgetCache(): void { Cache::forget('settings.map'); }

    /** Settings safe to expose to the public frontend (images converted to URLs). */
    public static function publicMap(): array
    {
        $map = static::map();
        $out = [];
        foreach (config('site.groups') as $g) {
            foreach ($g['fields'] as $f) {
                if (($f['public'] ?? true) === false) continue;
                $v = $map[$f['key']] ?? null;
                $out[$f['key']] = ($f['type'] === 'image') ? Media::url($v) : $v;
            }
        }
        return $out;
    }
}
