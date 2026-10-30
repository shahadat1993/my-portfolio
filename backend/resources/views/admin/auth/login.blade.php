<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/assets') }}/">
<head>
  <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin login · {{ \App\Models\Setting::get('site_name', 'Portfolio') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/pages/page-auth.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}" />
</head>
<body class="login-bg">
<div class="container-xxl"><div class="authentication-wrapper authentication-basic container-p-y"><div class="authentication-inner login-card">
  <div class="card"><div class="card-body">
    <div class="app-brand justify-content-center"><span class="app-brand-text demo text-body fw-bolder"><i class="bx bxs-bolt-circle text-primary me-1" style="font-size:34px;vertical-align:-6px"></i>{{ \App\Models\Setting::get('site_name', 'Portfolio') }}</span></div>
    <h4 class="mb-2 text-center">Welcome back 👋</h4>
    <p class="mb-4 text-center text-muted">Sign in to manage your portfolio</p>
    <form method="POST" action="{{ route('admin.login.post') }}">@csrf
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autofocus required />@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required /></div>
      <div class="mb-3 form-check"><input class="form-check-input" type="checkbox" name="remember" id="rem" /><label class="form-check-label" for="rem">Remember me</label></div>
      <button class="btn btn-primary d-grid w-100" type="submit"><span><i class="bx bx-log-in-circle me-1"></i> Sign in</span></button>
    </form>
  </div></div>
</div></div></div>
<script src="{{ asset('admin-assets/vendor/gsap.min.js') }}"></script>
<script>gsap.from('.login-card .card',{y:40,opacity:0,duration:.9,ease:'power3.out'});gsap.from('.login-card .card-body>*',{y:20,opacity:0,stagger:.08,delay:.25,duration:.6});</script>
</body></html>
