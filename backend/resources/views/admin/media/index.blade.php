@extends('admin.layouts.app')
@section('title', 'Media library')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2"><div><h4 class="fw-bold mb-0"><i class="bx bx-images me-2 text-primary"></i>Media library</h4><small class="text-muted">Every image / video uploaded anywhere in the admin lives here</small></div>
  <form class="d-flex gap-2"><select name="type" class="form-select" onchange="this.form.submit()"><option value="">All</option><option value="image" @selected($type === 'image')>Images</option><option value="video" @selected($type === 'video')>Videos</option></select><input class="form-control" name="q" value="{{ $q }}" placeholder="Search…"></form></div>
<div class="card mb-4 anim-card"><div class="card-body"><form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="dropzone-lite" id="dz">@csrf
  <i class="bx bx-cloud-upload fs-1 text-primary"></i><div class="fw-semibold">Drop files here or click to choose</div><small class="text-muted">Images (jpg, png, webp, gif, svg), videos (mp4, webm, mov) and PDFs — up to 200 MB each</small>
  <input type="file" name="files[]" multiple class="form-control mt-3" onchange="this.form.submit()"></form></div></div>
<div class="row g-3">
@forelse($items as $m)
  <div class="col-6 col-md-4 col-xl-2"><div class="card media-card anim-card h-100"><div class="media-thumb">@if($m->type === 'image')<img src="{{ $m->url }}" alt="">@elseif($m->type === 'video')<video src="{{ $m->url }}" muted preload="metadata"></video><i class="bx bx-play-circle play"></i>@else<i class="bx bx-file fs-1"></i>@endif</div>
    <div class="card-body p-2"><div class="small fw-semibold text-truncate" title="{{ $m->title }}">{{ $m->title ?: basename($m->path) }}</div><div class="small text-muted">{{ number_format($m->size / 1024) }} KB @if($m->width)· {{ $m->width }}×{{ $m->height }}@endif</div>
      <div class="d-flex gap-1 mt-2"><button class="btn btn-sm btn-outline-primary flex-grow-1 copy-url" data-url="{{ $m->url }}"><i class="bx bx-copy"></i> URL</button>
      <form method="POST" action="{{ route('admin.media.destroy', $m) }}" data-confirm="Delete this file permanently?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form></div></div></div></div>
@empty<div class="col-12 text-center text-muted py-5">No files yet.</div>@endforelse
</div>
<div class="mt-4">{{ $items->links('pagination::bootstrap-5') }}</div>
@endsection
