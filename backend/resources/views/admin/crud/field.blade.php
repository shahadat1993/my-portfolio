@php
  $n = $f['name']; $t = $f['type'];
  $val = old($n, $item ? data_get($item, $n) : ($f['default'] ?? null));
  if (in_array($t, ['tags']) && is_array($val)) $val = implode(', ', $val);
  if ($t === 'list' && is_array($val)) $val = implode("\n", $val);
  if ($t === 'datetime' && $val) $val = \Illuminate\Support\Carbon::parse($val)->format('Y-m-d\TH:i');
@endphp
<label class="form-label fw-semibold" for="f_{{ $n }}">{{ $f['label'] }}</label>
@switch($t)
  @case('textarea')
    <textarea id="f_{{ $n }}" name="{{ $n }}" rows="{{ $f['rows'] ?? 4 }}" class="form-control @error($n) is-invalid @enderror">{{ $val }}</textarea>
    @break
  @case('list')
    <textarea id="f_{{ $n }}" name="{{ $n }}" rows="{{ $f['rows'] ?? 5 }}" class="form-control" placeholder="One item per line">{{ $val }}</textarea>
    @break
  @case('tags')
    <input id="f_{{ $n }}" type="text" name="{{ $n }}" value="{{ $val }}" class="form-control" placeholder="Laravel, React, MySQL" />
    @break
  @case('number')
    <input id="f_{{ $n }}" type="number" name="{{ $n }}" value="{{ $val }}" class="form-control @error($n) is-invalid @enderror" />
    @break
  @case('color')
    <div class="input-group"><input type="color" class="form-control form-control-color" value="{{ $val ?: '#ff6a3d' }}" oninput="this.nextElementSibling.value=this.value" /><input id="f_{{ $n }}" type="text" name="{{ $n }}" value="{{ $val ?: '#ff6a3d' }}" class="form-control" oninput="this.previousElementSibling.value=this.value" /></div>
    @break
  @case('datetime')
    <input id="f_{{ $n }}" type="datetime-local" name="{{ $n }}" value="{{ $val }}" class="form-control" />
    @break
  @case('select')
    <select id="f_{{ $n }}" name="{{ $n }}" class="form-select @error($n) is-invalid @enderror">
      @if(isset($f['empty']))<option value="">{{ $f['empty'] }}</option>@endif
      @foreach($f['options'] as $k => $label)<option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $label }}</option>@endforeach
    </select>
    @break
  @case('toggle')
    <div class="form-check form-switch fs-5 mt-1"><input type="hidden" name="{{ $n }}" value="0" /><input class="form-check-input" type="checkbox" role="switch" id="f_{{ $n }}" name="{{ $n }}" value="1" @checked($val) /></div>
    @break
  @case('image')
    <div class="upload-box">
      <div class="preview-wrap">@if($item && $item->{$n})<img src="{{ \App\Models\Media::url($item->{$n}) }}" class="preview" alt="">@else<div class="preview empty"><i class="bx bx-image-add"></i></div>@endif</div>
      <div class="flex-grow-1"><input id="f_{{ $n }}" type="file" name="{{ $n }}" accept="image/*" class="form-control img-input" />
        @if($item && $item->{$n})<div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_{{ $n }}" value="1" id="rm_{{ $n }}"><label class="form-check-label" for="rm_{{ $n }}">Remove current image</label></div>@endif</div>
    </div>
    @break
  @case('video')
    <div class="upload-box">
      <div class="preview-wrap">@if($item && $item->{$n} && !preg_match('#^https?://#', $item->{$n}))<video src="{{ \App\Models\Media::url($item->{$n}) }}" class="preview" muted controls></video>@elseif($item && $item->{$n})<div class="preview empty"><i class="bx bx-link"></i></div>@else<div class="preview empty"><i class="bx bx-video-plus"></i></div>@endif</div>
      <div class="flex-grow-1">
        <input id="f_{{ $n }}" type="file" name="{{ $n }}" accept="video/mp4,video/webm,video/quicktime" class="form-control mb-2" />
        <input type="text" name="{{ $n }}_url" value="{{ $item && $item->{$n} && preg_match('#^https?://#', $item->{$n}) ? $item->{$n} : '' }}" class="form-control" placeholder="…or paste a YouTube / Vimeo / .mp4 URL" />
        @if($item && $item->{$n})<div class="small text-muted mt-1">Current: {{ \Illuminate\Support\Str::limit($item->{$n}, 60) }}</div><div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_{{ $n }}" value="1" id="rm_{{ $n }}"><label class="form-check-label" for="rm_{{ $n }}">Remove video</label></div>@endif
      </div>
    </div>
    @break
  @case('gallery')
    <div class="gallery-grid" id="gal_{{ $n }}">
      @foreach(($item ? ($item->{$n} ?? []) : []) as $i => $g)
        <div class="gal-item"><img src="{{ \App\Models\Media::url($g['src'] ?? '') }}" alt="">
          <input type="hidden" name="{{ $n }}_items[{{ $i }}][src]" value="{{ $g['src'] ?? '' }}">
          <input type="text" class="form-control form-control-sm mt-1" name="{{ $n }}_items[{{ $i }}][caption]" value="{{ $g['caption'] ?? '' }}" placeholder="Caption">
          <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="{{ $n }}_items[{{ $i }}][keep]" value="1" checked id="g_{{ $n }}_{{ $i }}"><label class="form-check-label small" for="g_{{ $n }}_{{ $i }}">Keep</label></div></div>
      @endforeach
    </div>
    <input id="f_{{ $n }}" type="file" name="{{ $n }}_new[]" accept="image/*" multiple class="form-control mt-2" />
    <div class="form-text">Select several images at once to add them. Untick “Keep” to remove an existing image.</div>
    @break
  @case('editor')
    <textarea id="f_{{ $n }}" name="{{ $n }}" class="rte" data-height="{{ $f['height'] ?? 480 }}">{{ $val }}</textarea>
    @break
  @default
    <input id="f_{{ $n }}" type="text" name="{{ $n }}" value="{{ $val }}" class="form-control @error($n) is-invalid @enderror" placeholder="{{ $f['placeholder'] ?? '' }}" />
@endswitch
@error($n)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
@if(!empty($f['help']))<div class="form-text">{{ $f['help'] }}</div>@endif
