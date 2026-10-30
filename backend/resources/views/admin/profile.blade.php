@extends('admin.layouts.app')
@section('title', 'My profile')
@section('content')
<h4 class="fw-bold mb-4"><i class="bx bx-user me-2 text-primary"></i>My profile</h4>
<div class="card anim-card" style="max-width:640px"><div class="card-body"><form method="POST" action="{{ route('admin.profile.update') }}">@csrf
  <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
  <div class="mb-3"><label class="form-label">Email (login)</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
  <hr><p class="text-muted small">Leave blank to keep your current password.</p>
  <div class="mb-3"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control"></div>
  <div class="row"><div class="col-md-6 mb-3"><label class="form-label">New password</label><input type="password" name="password" class="form-control"></div><div class="col-md-6 mb-3"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control"></div></div>
  <button class="btn btn-primary"><i class="bx bx-save me-1"></i> Update profile</button></form></div></div>
@endsection
