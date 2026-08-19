@extends('admin.layout')
@section('content')
<div class="admin-page-head"><div><span>ADMINISTRATOR</span><h2>Account & security</h2><p>Update the account used to access the RozeHub control center.</p></div></div>
<form class="admin-form-card" method="POST" action="{{ route('admin.account.update') }}">@csrf @method('PUT')
<div class="form-grid two"><label>Name<input name="name" value="{{ old('name',auth()->user()->name) }}" required></label><label>Email<input type="email" name="email" value="{{ old('email',auth()->user()->email) }}" required></label></div>
<label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label>
<div class="form-grid two"><label>New password <span class="hint">Minimum 12 characters</span><input type="password" name="password" autocomplete="new-password"></label><label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password"></label></div>
<div class="form-actions"><button class="admin-primary" type="submit">Save security settings</button></div>
</form>
@endsection
