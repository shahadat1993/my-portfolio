@extends('admin.layouts.app')
@section('title', 'Message from ' . $m->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-bold mb-0"><i class="bx bx-envelope-open me-2 text-primary"></i>Message</h4><a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Back</a></div>
<div class="row g-4"><div class="col-lg-8"><div class="card anim-card"><div class="card-body">
  <div class="d-flex align-items-center mb-4"><span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center me-3" style="width:52px;height:52px;font-size:20px">{{ strtoupper(substr($m->name, 0, 1)) }}</span><div><h5 class="mb-0">{{ $m->name }}</h5><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></div><div class="ms-auto text-muted small">{{ $m->created_at->format('M d, Y · H:i') }}</div></div>
  <div class="msg-body">{{ $m->message }}</div>
  <a href="mailto:{{ $m->email }}?subject=Re: your message" class="btn btn-primary mt-4"><i class="bx bx-reply me-1"></i> Reply by email</a>
</div></div></div>
<div class="col-lg-4"><div class="card anim-card"><div class="card-body">
  <dl class="mb-3"><dt class="small text-muted">Project type</dt><dd>{{ $m->project_type ?: '—' }}</dd><dt class="small text-muted">Budget</dt><dd>{{ $m->budget ?: '—' }}</dd><dt class="small text-muted">Delivery</dt><dd><span class="badge bg-label-{{ $m->mail_sent ? 'success' : 'secondary' }}">Email {{ $m->mail_sent ? 'sent' : 'not sent' }}</span> <span class="badge bg-label-{{ $m->sheet_synced ? 'success' : 'secondary' }}">Sheet {{ $m->sheet_synced ? 'synced' : 'not synced' }}</span></dd><dt class="small text-muted">IP</dt><dd>{{ $m->ip }}</dd></dl>
  <form method="POST" action="{{ route('admin.messages.update', $m) }}">@csrf @method('PUT')
    <label class="form-label">Status</label><select name="status" class="form-select mb-3">@foreach(['new', 'read', 'replied'] as $s)<option value="{{ $s }}" @selected($m->status === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <label class="form-label">Private note</label><textarea name="note" rows="4" class="form-control mb-3" placeholder="Follow-up, quote sent…">{{ $m->note }}</textarea><button class="btn btn-outline-primary w-100">Save</button></form>
</div></div></div></div>
@endsection
