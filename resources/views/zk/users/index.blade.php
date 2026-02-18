@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Device Users</h1>

    <a href="{{ route('zk.users.create') }}" class="btn">+ Add User</a>

    <table>
        <thead>
            <tr>
                <th>UID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user['uid'] }}</td>
                <td>{{ $user['userid'] }}</td>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['role'] }}</td>
                <td>
                    <a href="{{ route('zk.users.edit', $user['uid']) }}">Edit</a>

                    <form action="{{ route('zk.users.destroy', $user['uid']) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
