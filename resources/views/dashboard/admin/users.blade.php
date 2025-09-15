@extends('layouts.dashboard')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('content')
<div class="container">
    <h2 class="mb-4">Manage Users & Roles</h2>
    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('admin.create-user') }}" class="btn btn-primary">Create Privileged User</a>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Current Role(s)</th>
                        <th>Assign Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </td>
                        <td>
                            <form action="{{ route('admin.assign-role', $user) }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                <select name="role" class="form-select form-select-sm" style="width:auto;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @if($user->roles->contains('name', $role->name)) selected @endif>{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Assign</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
