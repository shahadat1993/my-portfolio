<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\Uploader;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $r)
    {
        $q = Media::query();
        if ($t = $r->query('type')) $q->where('type', $t);
        if ($s = trim((string) $r->query('q'))) $q->where('title', 'like', "%$s%");
        return view('admin.media.index', ['items' => $q->latest('id')->paginate(24)->withQueryString(), 'type' => $t, 'q' => $s]);
    }

    public function store(Request $r)
    {
        $r->validate(['files' => 'required|array', 'files.*' => 'file|max:204800|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,video/mp4,video/webm,video/quicktime,application/pdf']);
        foreach ($r->file('files') as $f) Uploader::store($f, 'library');
        return back()->with('ok', count($r->file('files')) . ' file(s) uploaded.');
    }

    public function destroy(Media $medium)
    {
        Uploader::delete($medium->path);
        $medium->delete();
        return back()->with('ok', 'File deleted.');
    }

    /** JSON list used by the editor's "Browse library" picker. */
    public function list(Request $r)
    {
        $q = Media::query()->latest('id');
        if ($t = $r->query('type')) $q->where('type', $t);
        return response()->json($q->limit(60)->get()->map(fn ($m) => ['id' => $m->id, 'url' => $m->url, 'title' => $m->title, 'type' => $m->type]));
    }

    /** TinyMCE image upload handler → { location: url } */
    public function editorUpload(Request $r)
    {
        $r->validate(['file' => 'required|file|image|max:12288']);
        $path = Uploader::store($r->file('file'), 'editor');
        return response()->json(['location' => '/storage/' . $path]); // relative: API rewrites to the right host
    }
}
