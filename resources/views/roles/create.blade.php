@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Create Role</h2>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Role Name</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="form-group mt-3">
            <label for="permissions">Permissions</label>
            <div id="permissions"> 
                @foreach ($permissions as $permission)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}" value="{{ $permission->id }}">
                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Role</button>
    </form>
</div>
@endsection
