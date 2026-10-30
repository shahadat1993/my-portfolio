<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Generic admin CRUD. A concrete controller only describes: model(), meta(), fields(), columns().
 * Field types: section, text, textarea, number, select, toggle, color, datetime,
 *              image, video, gallery, editor (rich text), tags, list (one per line).
 */
abstract class CrudController extends Controller
{
    abstract protected function model(): string;
    abstract protected function meta(): array;      // title, singular, route, icon
    abstract protected function fields(): array;

    protected function columns(): array { return [['key' => 'id', 'label' => '#']]; }
    protected function searchable(): array { return ['title']; }
    protected function with(): array { return []; }
    protected function order($q) { return $q->latest('id'); }
    protected function beforeSave(array &$d, $m = null): void {}
    protected function afterSave($m): void {}

    public function index(Request $r)
    {
        $s = trim((string) $r->query('q', ''));
        $q = ($this->model())::query()->with($this->with());
        if ($s !== '') $q->where(function ($w) use ($s) { foreach ($this->searchable() as $c) $w->orWhere($c, 'like', "%{$s}%"); });
        $items = $this->order($q)->paginate(12)->withQueryString();
        return view('admin.crud.index', ['meta' => $this->meta(), 'columns' => $this->columns(), 'items' => $items, 'q' => $s]);
    }

    public function create()
    {
        return view('admin.crud.form', ['meta' => $this->meta(), 'fields' => $this->fields(), 'item' => null]);
    }

    public function store(Request $r)
    {
        $d = $this->validated($r, null);
        $this->beforeSave($d, null);
        $m = ($this->model())::create($d);
        $this->afterSave($m);
        return redirect()->route('admin.' . $this->meta()['route'] . '.edit', $m)->with('ok', $this->meta()['singular'] . ' created.');
    }

    public function edit($id)
    {
        $m = ($this->model())::findOrFail($id);
        return view('admin.crud.form', ['meta' => $this->meta(), 'fields' => $this->fields(), 'item' => $m]);
    }

    public function update(Request $r, $id)
    {
        $m = ($this->model())::findOrFail($id);
        $d = $this->validated($r, $m);
        $this->beforeSave($d, $m);
        $m->update($d);
        $this->afterSave($m->fresh());
        return back()->with('ok', $this->meta()['singular'] . ' saved.');
    }

    public function destroy($id)
    {
        $m = ($this->model())::findOrFail($id);
        foreach ($this->fields() as $f) {
            $n = $f['name'] ?? null;
            if (!$n) continue;
            if (in_array($f['type'], ['image', 'video'])) Uploader::delete($m->{$n});
            if ($f['type'] === 'gallery') foreach ((array) $m->{$n} as $g) Uploader::delete($g['src'] ?? null);
        }
        $m->delete();
        return redirect()->route('admin.' . $this->meta()['route'] . '.index')->with('ok', $this->meta()['singular'] . ' deleted.');
    }

    protected function validated(Request $r, $m): array
    {
        $rules = [];
        $table = (new ($this->model()))->getTable();
        foreach ($this->fields() as $f) {
            if (empty($f['name'])) continue;
            if ($f['name'] === 'slug') { $rules['slug'] = ['nullable', 'regex:/^[a-z0-9\-]+$/', Rule::unique($table, 'slug')->ignore($m?->id)]; continue; }
            if (!empty($f['rules'])) $rules[$f['name']] = $f['rules'];
            if ($f['type'] === 'image') $rules[$f['name']] = $rules[$f['name']] ?? 'nullable|image|max:8192';
            if ($f['type'] === 'video') $rules[$f['name']] = $rules[$f['name']] ?? 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:204800';
        }
        $r->validate($rules);
        $d = $this->collect($r, $m);
        if (in_array('slug', array_column($this->fields(), 'name')) && empty($d['slug'])) {
            if ($m && $m->slug) { $d['slug'] = $m->slug; return $d; }
            $base = Str::slug((string) ($r->input('title') ?: $r->input('name'))) ?: 'item';
            $slug = $base; $i = 2;
            while (($this->model())::where('slug', $slug)->when($m, fn ($q) => $q->where('id', '!=', $m->id))->exists()) $slug = $base . '-' . $i++;
            $d['slug'] = $slug;
        }
        return $d;
    }

    protected function collect(Request $r, $m): array
    {
        $d = [];
        foreach ($this->fields() as $f) {
            $n = $f['name'] ?? null; $t = $f['type'];
            if (!$n || $t === 'section') continue;
            $dir = $f['dir'] ?? $this->meta()['route'];
            switch ($t) {
                case 'toggle': $d[$n] = $r->boolean($n); break;
                case 'number': $d[$n] = $r->filled($n) ? (int) $r->input($n) : ($f['default'] ?? 0); break;
                case 'image':
                case 'video':
                    $old = $m?->{$n};
                    if ($r->hasFile($n)) { Uploader::delete($old); $d[$n] = Uploader::store($r->file($n), $dir); }
                    elseif ($t === 'video' && $r->filled($n . '_url')) { Uploader::delete($old); $d[$n] = trim($r->input($n . '_url')); }
                    elseif ($r->boolean('remove_' . $n)) { Uploader::delete($old); $d[$n] = null; }
                    break;
                case 'gallery':
                    $current = array_column((array) ($m?->{$n} ?? []), 'src');
                    $keep = [];
                    foreach ((array) $r->input($n . '_items', []) as $it) {
                        if (empty($it['src'])) continue;
                        if (!empty($it['keep'])) $keep[] = ['src' => $it['src'], 'caption' => $it['caption'] ?? ''];
                        elseif (in_array($it['src'], $current, true)) Uploader::delete($it['src']);
                    }
                    foreach ((array) $r->file($n . '_new', []) as $file) if ($file) $keep[] = ['src' => Uploader::store($file, $dir . '/gallery'), 'caption' => ''];
                    $d[$n] = $keep;
                    break;
                case 'tags': $d[$n] = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/', (string) $r->input($n))))); break;
                case 'list': $d[$n] = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $r->input($n))))); break;
                case 'datetime': $d[$n] = $r->filled($n) ? Carbon::parse($r->input($n)) : null; break;
                default: $d[$n] = $r->input($n);
            }
        }
        return $d;
    }
}
