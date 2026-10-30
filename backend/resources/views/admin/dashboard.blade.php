@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-4 mb-4">
  <div class="col-lg-8"><div class="card hero-card anim-card"><div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div><h4 class="mb-1 text-white">Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4><p class="mb-3 text-white-50">Manage projects, write long blog posts with images, read messages and tune the website look — all from here.</p>
      <a href="{{ route('admin.projects.create') }}" class="btn btn-light btn-sm me-2"><i class="bx bx-plus"></i> New project</a><a href="{{ route('admin.posts.create') }}" class="btn btn-outline-light btn-sm"><i class="bx bx-edit"></i> Write a post</a></div>
    <i class="bx bx-rocket hero-rocket"></i></div></div></div>
  <div class="col-lg-4"><div class="card h-100 anim-card"><div class="card-body"><h6 class="mb-3"><i class="bx bx-task me-1 text-primary"></i> Setup checklist</h6>
    @foreach($checklist as $c)<div class="d-flex align-items-center mb-2"><i class="bx {{ $c[1] ? 'bxs-check-circle text-success' : 'bx-circle text-muted' }} fs-5 me-2"></i>@if($c[2] && !$c[1])<a href="{{ $c[2] }}" class="small">{{ $c[0] }}</a>@else<span class="small {{ $c[1] ? 'text-muted' : '' }}">{{ $c[0] }}</span>@endif</div>@endforeach
  </div></div></div>
</div>
<div class="row g-4 mb-4">
  @foreach($stats as $s)
  <div class="col-sm-6 col-xl-3"><a href="{{ $s[4] }}" class="card stat-card anim-card text-body"><div class="card-body"><div class="d-flex justify-content-between align-items-start"><div><span class="text-muted d-block mb-1">{{ $s[0] }}</span><h2 class="mb-1 count" data-n="{{ $s[1] }}">0</h2><small class="text-muted">{{ $s[5] }}</small></div><span class="avatar-initial rounded bg-label-{{ $s[3] }} p-3"><i class="bx {{ $s[2] }} fs-3"></i></span></div></div></a></div>
  @endforeach
</div>
<div class="row g-4">
  <div class="col-lg-7"><div class="card anim-card h-100"><div class="card-header d-flex justify-content-between"><h5 class="mb-0">Messages (last 6 months)</h5><a href="{{ route('admin.messages.export') }}" class="btn btn-sm btn-outline-success"><i class="bx bx-spreadsheet"></i> Excel</a></div><div class="card-body"><div id="msgChart"></div></div></div></div>
  <div class="col-lg-5"><div class="card anim-card h-100"><div class="card-header"><h5 class="mb-0">Most read posts</h5></div><ul class="list-group list-group-flush">
    @forelse($topPosts as $p)<li class="list-group-item d-flex align-items-center"><img src="{{ $p->cover_url ?: asset('sneat/assets/img/elements/2.jpg') }}" class="rounded me-3 thumb" alt=""><a href="{{ route('admin.posts.edit', $p) }}" class="flex-grow-1 text-body fw-semibold">{{ \Illuminate\Support\Str::limit($p->title, 42) }}</a><span class="badge bg-label-info"><i class="bx bx-show"></i> {{ $p->views }}</span></li>@empty<li class="list-group-item text-muted">No posts yet.</li>@endforelse</ul></div></div>
  <div class="col-12"><div class="card anim-card"><div class="card-header d-flex justify-content-between"><h5 class="mb-0">Recent messages</h5><a href="{{ route('admin.messages.index') }}">View all</a></div>
    <div class="table-responsive"><table class="table table-hover mb-0"><tbody>
      @forelse($recent as $m)<tr class="row-anim"><td><span class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px">{{ strtoupper(substr($m->name, 0, 1)) }}</span></td><td><a href="{{ route('admin.messages.show', $m) }}" class="fw-semibold text-body">{{ $m->name }}</a><div class="small text-muted">{{ $m->email }}</div></td><td class="text-muted d-none d-md-table-cell">{{ \Illuminate\Support\Str::limit($m->message, 70) }}</td><td><span class="badge bg-label-{{ ['new' => 'danger', 'read' => 'secondary', 'replied' => 'success'][$m->status] ?? 'secondary' }}">{{ $m->status }}</span></td><td class="text-muted small">{{ $m->created_at->diffForHumans() }}</td></tr>@empty<tr><td class="text-center text-muted py-4">No messages yet — they will appear here (and in your email / Google Sheet).</td></tr>@endforelse
    </tbody></table></div></div></div>
</div>
@endsection
@push('scripts')
<script>
new ApexCharts(document.querySelector('#msgChart'),{chart:{type:'area',height:260,toolbar:{show:false},fontFamily:'Public Sans'},series:[{name:'Messages',data:@json($chart['data'])}],xaxis:{categories:@json($chart['labels'])},colors:['#696cff'],stroke:{curve:'smooth',width:3},dataLabels:{enabled:false},fill:{type:'gradient',gradient:{opacityFrom:.45,opacityTo:.05}},yaxis:{labels:{formatter:v=>Math.round(v)}}}).render();
</script>
@endpush
