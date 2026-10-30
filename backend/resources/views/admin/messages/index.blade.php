@extends('admin.layouts.app')
@section('title', 'Messages')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <div><h4 class="fw-bold mb-0"><i class="bx bx-envelope me-2 text-primary"></i>Messages</h4><small class="text-muted">Everything sent from the website contact form</small></div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.messages.export') }}" class="btn btn-success"><i class="bx bx-spreadsheet me-1"></i> Download Excel</a>
    <a href="{{ route('admin.messages.export', ['format' => 'csv']) }}" class="btn btn-outline-success">CSV</a>
    <form method="POST" action="{{ route('admin.messages.sync') }}">@csrf<button class="btn btn-outline-primary" @disabled(!$sheet)><i class="bx bxl-google me-1"></i> Sync to Google Sheet</button></form>
  </div>
</div>
<ul class="nav nav-pills mb-3">
  @foreach(['' => 'all', 'new' => 'new', 'read' => 'read', 'replied' => 'replied'] as $k => $lbl)
    <li class="nav-item"><a class="nav-link {{ ($status ?? '') === $k ? 'active' : '' }}" href="{{ route('admin.messages.index', array_filter(['status' => $k, 'q' => $q])) }}">{{ ucfirst($lbl) }} <span class="badge bg-white text-dark ms-1">{{ $counts[$lbl] }}</span></a></li>
  @endforeach
  <li class="nav-item ms-auto"><form class="d-flex"><input type="hidden" name="status" value="{{ $status }}"><input class="form-control" type="search" name="q" value="{{ $q }}" placeholder="Search messages…"></form></li>
</ul>
<div class="card anim-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>From</th><th>Message</th><th>Type / budget</th><th>Status</th><th>Mail</th><th>Sheet</th><th>Date</th><th></th></tr></thead><tbody>
  @forelse($items as $m)
    <tr class="row-anim {{ $m->status === 'new' ? 'fw-semibold' : '' }}">
      <td><a href="{{ route('admin.messages.show', $m) }}" class="text-body">{{ $m->name }}</a><div class="small text-muted fw-normal">{{ $m->email }}</div></td>
      <td class="text-muted fw-normal" style="max-width:340px">{{ \Illuminate\Support\Str::limit($m->message, 80) }}</td>
      <td class="small fw-normal">{{ $m->project_type ?: '—' }}<div class="text-muted">{{ $m->budget }}</div></td>
      <td><span class="badge bg-label-{{ ['new' => 'danger', 'read' => 'secondary', 'replied' => 'success'][$m->status] ?? 'secondary' }}">{{ $m->status }}</span></td>
      <td><i class="bx {{ $m->mail_sent ? 'bxs-check-circle text-success' : 'bx-x-circle text-muted' }} fs-5"></i></td>
      <td><i class="bx {{ $m->sheet_synced ? 'bxs-check-circle text-success' : 'bx-x-circle text-muted' }} fs-5"></i></td>
      <td class="small text-muted fw-normal">{{ $m->created_at->format('M d, H:i') }}</td>
      <td class="text-end"><a href="{{ route('admin.messages.show', $m) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-show"></i></a>
        <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" class="d-inline" data-confirm="Delete this message?">@csrf @method('DELETE')<button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button></form></td>
    </tr>
  @empty<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bx bx-envelope fs-1 d-block mb-2"></i>No messages yet.</td></tr>@endforelse
  </tbody></table></div>@if($items->hasPages())<div class="card-footer">{{ $items->links('pagination::bootstrap-5') }}</div>@endif</div>
@endsection
