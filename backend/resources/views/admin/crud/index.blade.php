@extends('admin.layouts.app')
@section('title', $meta['title'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <div><h4 class="fw-bold mb-0"><i class="bx {{ $meta['icon'] }} me-2 text-primary"></i>{{ $meta['title'] }}</h4><small class="text-muted">{{ $items->total() }} item(s)</small></div>
  <div class="d-flex gap-2">
    <form method="GET" class="d-flex"><input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Filter…" /><button class="btn btn-outline-secondary ms-2"><i class="bx bx-search"></i></button></form>
    <a href="{{ route('admin.' . $meta['route'] . '.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> New {{ strtolower($meta['singular']) }}</a>
  </div>
</div>
<div class="card anim-card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr>@foreach($columns as $c)<th>{{ $c['label'] }}</th>@endforeach<th class="text-end">Actions</th></tr></thead>
      <tbody>
      @forelse($items as $row)
        <tr class="row-anim">
          @foreach($columns as $c)
            @php $v = data_get($row, $c['key']); $t = $c['type'] ?? 'text'; @endphp
            <td>
              @if($t === 'image')
                @if($v)<img src="{{ $v }}" class="rounded thumb" alt="">@else<span class="thumb thumb-empty rounded"><i class="bx bx-image"></i></span>@endif
              @elseif($t === 'title')
                <a href="{{ route('admin.' . $meta['route'] . '.edit', $row) }}" class="fw-semibold text-body">{{ $v }}</a>@if(!empty($c['sub']))<div class="small text-muted">{{ data_get($row, $c['sub']) }}</div>@endif
              @elseif($t === 'badge')
                @if($v)<span class="badge bg-label-primary">{{ $v }}</span>@endif
              @elseif($t === 'bool')
                <i class="bx {{ $v ? 'bxs-check-circle text-success' : 'bx-x-circle text-muted' }} fs-4"></i>
              @elseif($t === 'date')
                {{ $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '—' }}
              @elseif($t === 'color')
                <span class="swatch" style="background:{{ $v }}"></span>
              @else
                {{ is_array($v) ? implode(', ', $v) : $v }}
              @endif
            </td>
          @endforeach
          <td class="text-end text-nowrap">
            <a href="{{ route('admin.' . $meta['route'] . '.edit', $row) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bx bx-edit-alt"></i></a>
            <form method="POST" action="{{ route('admin.' . $meta['route'] . '.destroy', $row) }}" class="d-inline" data-confirm="Delete this {{ strtolower($meta['singular']) }}?">@csrf @method('DELETE')<button class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></button></form>
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ count($columns) + 1 }}" class="text-center py-5 text-muted"><i class="bx {{ $meta['icon'] }} fs-1 d-block mb-2"></i>Nothing here yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())<div class="card-footer">{{ $items->links('pagination::bootstrap-5') }}</div>@endif
</div>
@endsection
