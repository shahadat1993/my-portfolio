@php
  $items = [
    ['Dashboard', 'admin.dashboard', 'bx-home-circle', 'admin.dashboard'],
    ['h', 'Content'],
    ['Projects', 'admin.projects.index', 'bx-briefcase', 'admin.projects.*'],
    ['Blog posts', 'admin.posts.index', 'bx-edit', 'admin.posts.*'],
    ['Categories', 'admin.categories.index', 'bx-category', 'admin.categories.*'],
    ['h', 'About & home'],
    ['Skills', 'admin.skills.index', 'bx-bulb', 'admin.skills.*'],
    ['Services', 'admin.services.index', 'bx-layer', 'admin.services.*'],
    ['Journey timeline', 'admin.timeline.index', 'bx-git-branch', 'admin.timeline.*'],
    ['FAQs', 'admin.faqs.index', 'bx-help-circle', 'admin.faqs.*'],
    ['h', 'Inbox'],
    ['Messages', 'admin.messages.index', 'bx-envelope', 'admin.messages.*', \App\Models\ContactMessage::where('status', 'new')->count()],
    ['Subscribers', 'admin.subscribers.index', 'bx-bell', 'admin.subscribers.*'],
    ['h', 'Appearance & files'],
    ['Theme colours', 'admin.colors.index', 'bx-palette', 'admin.colors.*'],
    ['Media library', 'admin.media.index', 'bx-images', 'admin.media.*'],
    ['Site settings', 'admin.settings', 'bx-cog', 'admin.settings*'],
    ['h', 'Account'],
    ['My profile', 'admin.profile', 'bx-user', 'admin.profile*'],
  ];
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo"><i class="bx bxs-bolt-circle text-primary" style="font-size:30px"></i></span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ \Illuminate\Support\Str::limit(\App\Models\Setting::get('site_name', 'Portfolio'), 12, '') }}</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i class="bx bx-chevron-left bx-sm align-middle"></i></a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    @foreach($items as $it)
      @if($it[0] === 'h')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ $it[1] }}</span></li>
      @else
        <li class="menu-item {{ request()->routeIs($it[3]) ? 'active' : '' }}">
          <a href="{{ route($it[1]) }}" class="menu-link">
            <i class="menu-icon tf-icons bx {{ $it[2] }}"></i>
            <div>{{ $it[0] }}</div>
            @if(!empty($it[4]))<div class="badge bg-danger rounded-pill ms-auto">{{ $it[4] }}</div>@endif
          </a>
        </li>
      @endif
    @endforeach
    <li class="menu-item"><a href="{{ config('site.frontend_url') }}" target="_blank" class="menu-link"><i class="menu-icon tf-icons bx bx-link-external"></i><div>Open website</div></a></li>
  </ul>
</aside>
