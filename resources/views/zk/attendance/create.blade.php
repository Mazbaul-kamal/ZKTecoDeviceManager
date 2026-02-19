@extends('layouts.app')

@section('content')
<div class="container">

<h1>Create User</h1>

<form method="POST" action="{{ route('zk.users.store') }}">
@csrf

<input type="number" name="uid" placeholder="UID" required>
<input type="text" name="userid" placeholder="User ID" required>
<input type="text" name="name" placeholder="Name" required>
<input type="password" name="password" placeholder="Password">
<input type="number" name="role" placeholder="Role (0=user,14=admin)">
<input type="number" name="cardno" placeholder="Card No">

<button type="submit">Save</button>

</form>

</div>
@endsection
