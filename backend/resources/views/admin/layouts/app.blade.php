<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets') }}/">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') · {{ \App\Models\Setting::get('site_name', 'Portfolio') }} Admin</title>
  <link rel="icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}" />
  @stack('styles')
  <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">
    @include('admin.partials.menu')
    <div class="layout-page">
      @include('admin.partials.navbar')
      <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y page-anim">
          @yield('content')
        </div>
        <footer class="content-footer footer bg-footer-theme">
          <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">© {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Portfolio') }} — admin dashboard</div>
            <div><a href="{{ config('site.frontend_url') }}" target="_blank" class="footer-link me-4"><i class="bx bx-link-external"></i> View website</a></div>
          </div>
        </footer>
        <div class="content-backdrop fade"></div>
      </div>
    </div>
  </div>
  <div class="layout-overlay layout-menu-toggle"></div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2000" id="toasts">
  @if(session('ok'))<div class="bs-toast toast fade show bg-success" role="alert"><div class="toast-body text-white"><i class="bx bx-check-circle me-1"></i>{{ session('ok') }}</div></div>@endif
  @if(session('err'))<div class="bs-toast toast fade show bg-danger" role="alert"><div class="toast-body text-white"><i class="bx bx-error-circle me-1"></i>{{ session('err') }}</div></div>@endif
  @if($errors->any())<div class="bs-toast toast fade show bg-danger" role="alert"><div class="toast-body text-white"><i class="bx bx-error-circle me-1"></i>{{ $errors->first() }}@if($errors->count() > 1) (+{{ $errors->count() - 1 }} more)@endif</div></div>@endif
</div>

<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/gsap.min.js') }}"></script>
<script>window.ADMIN = { search: @json(route('admin.search')), mediaList: @json(route('admin.media.list')), editorUpload: @json(route('admin.media.editor')), csrf: @json(csrf_token()), tinymce: @json(asset('vendor/tinymce')) };</script>
<script src="{{ asset('admin-assets/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
