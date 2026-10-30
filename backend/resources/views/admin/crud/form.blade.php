@extends('admin.layouts.app')
@section('title', ($item ? 'Edit ' : 'New ') . $meta['singular'])
@section('content')
@php $action = $item ? route('admin.' . $meta['route'] . '.update', $item) : route('admin.' . $meta['route'] . '.store'); @endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="crudForm">
  @csrf @if($item) @method('PUT') @endif
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 sticky-bar">
    <div><h4 class="fw-bold mb-0"><i class="bx {{ $meta['icon'] }} me-2 text-primary"></i>{{ $item ? 'Edit' : 'New' }} {{ strtolower($meta['singular']) }}</h4>
      @if($item)<small class="text-muted">ID #{{ $item->id }} · updated {{ $item->updated_at?->diffForHumans() }}</small>@endif</div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.' . $meta['route'] . '.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Back</a>
      <button class="btn btn-primary" type="submit"><i class="bx bx-save me-1"></i> Save</button>
    </div>
  </div>
  @php $open = false; @endphp
  @foreach($fields as $f)
    @if($f['type'] === 'section')
      @if($open)</div></div></div>@endif
      <div class="card mb-4 anim-card"><div class="card-header d-flex align-items-center"><i class="bx {{ $f['icon'] ?? 'bx-chevron-right' }} me-2 text-primary fs-4"></i><h5 class="mb-0">{{ $f['label'] }}</h5></div><div class="card-body"><div class="row g-3">
      @php $open = true; @endphp
    @else
      @if(!$open)<div class="card mb-4"><div class="card-body"><div class="row g-3">@php $open = true; @endphp @endif
      <div class="col-md-{{ $f['col'] ?? 12 }}">@include('admin.crud.field', ['f' => $f, 'item' => $item])</div>
    @endif
  @endforeach
  @if($open)</div></div></div>@endif
  <div class="d-flex justify-content-end gap-2 mb-5"><a href="{{ route('admin.' . $meta['route'] . '.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary btn-lg" type="submit"><i class="bx bx-save me-1"></i> Save {{ strtolower($meta['singular']) }}</button></div>
</form>
@endsection
