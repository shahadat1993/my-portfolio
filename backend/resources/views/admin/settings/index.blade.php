@extends('admin.layouts.app')
@section('title', 'Site settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">@csrf
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 sticky-bar"><div><h4 class="fw-bold mb-0"><i class="bx bx-cog me-2 text-primary"></i>Site settings</h4><small class="text-muted">Everything here appears on the website instantly (hero text, stats, contact email, social links…)</small></div><button class="btn btn-primary"><i class="bx bx-save me-1"></i> Save settings</button></div>
  <div class="card anim-card"><div class="card-header p-0"><ul class="nav nav-tabs nav-fill" role="tablist">
    @foreach($groups as $gk => $g)<li class="nav-item"><button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-{{ $gk }}" id="t-{{ $gk }}"><i class="bx {{ $g['icon'] }} me-1"></i><span class="d-none d-md-inline">{{ $g['label'] }}</span></button></li>@endforeach</ul></div>
  <div class="card-body tab-content pt-4">
  @foreach($groups as $gk => $g)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $gk }}"><div class="row g-3">
      @foreach($g['fields'] as $f)
        @php $k = $f['key']; $v = old($k, $values[$k] ?? ''); $wide = in_array($f['type'], ['textarea', 'image']); @endphp
        <div class="col-md-{{ $wide ? 12 : 6 }}">
          <label class="form-label fw-semibold" for="s_{{ $k }}">{{ $f['label'] }}</label>
          @if($f['type'] === 'textarea')<textarea id="s_{{ $k }}" name="{{ $k }}" rows="3" class="form-control">{{ $v }}</textarea>
          @elseif($f['type'] === 'toggle')<div class="form-check form-switch fs-5"><input type="hidden" name="{{ $k }}" value="0"><input class="form-check-input" type="checkbox" id="s_{{ $k }}" name="{{ $k }}" value="1" @checked($v === '1' || $v === 1 || $v === true)></div>
          @elseif($f['type'] === 'select')<select id="s_{{ $k }}" name="{{ $k }}" class="form-select">@foreach($f['options'] as $ok => $ol)<option value="{{ $ok }}" @selected($v == $ok)>{{ $ol }}</option>@endforeach</select>
          @elseif($f['type'] === 'image')<div class="upload-box"><div class="preview-wrap">@if($v)<img src="{{ \App\Models\Media::url($v) }}" class="preview" alt="">@else<div class="preview empty"><i class="bx bx-image-add"></i></div>@endif</div><div class="flex-grow-1"><input type="file" name="{{ $k }}" accept="image/*" class="form-control img-input">@if($v)<div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_{{ $k }}" value="1" id="rm_{{ $k }}"><label class="form-check-label" for="rm_{{ $k }}">Remove current image</label></div>@endif</div></div>
          @else<input id="s_{{ $k }}" type="{{ in_array($f['type'], ['number', 'email', 'url']) ? $f['type'] : 'text' }}" name="{{ $k }}" value="{{ $v }}" class="form-control">@endif
          @if(!empty($f['help']))<div class="form-text">{{ $f['help'] }}</div>@endif
        </div>
      @endforeach
    </div></div>
  @endforeach
  </div></div>
  <div class="text-end mt-4 mb-5"><button class="btn btn-primary btn-lg"><i class="bx bx-save me-1"></i> Save settings</button></div>
</form>
@endsection
@push('scripts')<script>if(location.hash){const t=document.querySelector('#t-'+location.hash.slice(1));t&&new bootstrap.Tab(t).show()}</script>@endpush
