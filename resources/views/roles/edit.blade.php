@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Edit Role - {{ $role->name }}</h2>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Role Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $role->name }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="permissions">Permissions</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple>
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}"
                        @if ($role->hasPermissionTo($permission->name)) selected @endif>
                        {{ $permission->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Role</button>
    </form>
</div>
@endsection
