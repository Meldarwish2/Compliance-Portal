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

    @push('scripts')
        <!-- Include Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Include Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

        <!-- Initialize Select2 -->
        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('#permissions').select2();
            });
        </script>
    @endpush

@endsection
