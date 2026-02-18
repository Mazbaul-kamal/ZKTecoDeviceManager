@extends('layouts.app')

@section('content')
<div class="container">

<h1>Edit User</h1>

<form method="POST" action="{{ route('zk.users.update', $user['uid']) }}">
@csrf
@method('PUT')

<input type="text" name="userid" value="{{ $user['userid'] }}" required>
<input type="text" name="name" value="{{ $user['name'] }}" required>
<input type="password" name="password" placeholder="New Password">
<input type="number" name="role" value="{{ $user['role'] }}">
<input type="number" name="cardno" value="{{ $user['cardno'] }}">

<button type="submit">Update</button>

</form>

</div>
@endsection
