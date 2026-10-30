@extends('admin.layouts.app')
@section('title', 'Subscribers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-bold mb-0"><i class="bx bx-bell me-2 text-primary"></i>Newsletter subscribers</h4><a href="{{ route('admin.subscribers.export') }}" class="btn btn-outline-success"><i class="bx bx-download"></i> Export CSV</a></div>
<div class="card anim-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Email</th><th>Subscribed</th><th></th></tr></thead><tbody>
@forelse($items as $s)<tr class="row-anim"><td>{{ $s->email }}</td><td class="text-muted">{{ $s->created_at->format('M d, Y H:i') }}</td><td class="text-end"><form method="POST" action="{{ route('admin.subscribers.destroy', $s) }}" data-confirm="Remove subscriber?">@csrf @method('DELETE')<button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button></form></td></tr>@empty<tr><td colspan="3" class="text-center py-5 text-muted">No subscribers yet.</td></tr>@endforelse
</tbody></table></div>@if($items->hasPages())<div class="card-footer">{{ $items->links('pagination::bootstrap-5') }}</div>@endif</div>
@endsection
