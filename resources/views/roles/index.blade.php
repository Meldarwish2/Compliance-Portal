@extends('layouts.master2')

@section('content')
<div class="container">
    <h2>Roles</h2>

    <!-- Button to Open Create Role Modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createRoleModal">Create Role</button>

    <table id="model-datatables" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
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
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="javascript:void(0);"
                                   class="dropdown-item edit-item-btn"
                                   onclick="openEditModal({{ $role->id }}, '{{ $role->name }}', {{ $role->permissions->pluck('id') }})">
                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item remove-item-btn">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <div id="permissions-checkbox-group">
                            @foreach ($allPermissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input" id="permission-{{ $permission->id }}">
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editRoleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="editRoleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="editRoleName" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editPermissions" class="form-label">Permissions</label>
                        <div id="edit-permissions-checkbox-group">
                            @foreach ($allPermissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input" id="edit-permission-{{ $permission->id }}">
                                    <label class="form-check-label" for="edit-permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Show all permissions for a role
    function showAllPermissions(roleId) {
        const container = document.getElementById(`permissions-${roleId}`);
        const allPermissions = container.querySelector('.all-permissions');
        allPermissions.classList.toggle('d-none');
        container.querySelector('.see-more-btn').classList.add('d-none');
    }

    // Confirm Delete
    function confirmDelete(event) {
        if (!confirm('Are you sure you want to delete this role?')) {
            event.preventDefault();
        }
    }

    // Populate Edit Modal
    function openEditModal(roleId, roleName, rolePermissions) {
        const form = document.getElementById('editRoleForm');
        form.action = `{{ url('roles') }}/${roleId}`;
        document.getElementById('editRoleName').value = roleName;

        // Set selected permissions
        rolePermissions.forEach(permissionId => {
            document.getElementById(`edit-permission-${permissionId}`).checked = true;
        });

        // Open the modal
        $('#editRoleModal').modal('show');
    }
</script>
@endsection
