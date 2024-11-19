@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Roles</h2>

        <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">Create Role</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        <div class="permissions-list" id="permissions-{{ $role->id }}">
                            @foreach ($role->permissions->take(3) as $permission)
                                <span class="badge bg-info">{{ $permission->name }}</span>
                            @endforeach

                            @if($role->permissions->count() > 3)
                                <button class="btn btn-link p-0 see-more-btn" onclick="showAllPermissions({{ $role->id }})">Show more</button>
                                <span class="d-none all-permissions">
                            @foreach ($role->permissions->skip(3) as $permission)
                                        <span class="badge bg-info">{{ $permission->name }}</span>
                                    @endforeach
                        </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm w-100 w-sm-auto">Edit</a>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100 w-sm-auto">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <style>
        .permissions-list .badge {
            display: inline-block; /* Ensures badges are inline */
            margin-right: 5px; /* Adds spacing between badges */
        }
        .permissions-list .all-permissions {
            display: inline; /* Keeps additional permissions inline */
        }
        .permissions-list .see-more-btn {
            margin-left: 10px;
        }
    </style>

    <script>
        function showAllPermissions(roleId) {
            const container = document.getElementById(`permissions-${roleId}`);
            const allPermissions = container.querySelector('.all-permissions');
            allPermissions.classList.toggle('d-none');
            container.querySelector('.see-more-btn').classList.add('d-none');
        }
    </script>
@endsection
