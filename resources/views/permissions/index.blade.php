@extends('layouts.master2')

@section('content')
<div class="container">
    <h2>Permissions</h2>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
        Create Permission
    </button>

    <table id="model-datatables" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    
                    <td>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item edit-item-btn"
                                            onclick="openEditModal({{ $permission->id }}, '{{ $permission->name }}', '{{ $permission->email }}', {{ $permission->roles->pluck('id') }})">
                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                        </a>

                                        <li>
                                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event)">
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

<!-- Create Permission Modal -->
<div id="createPermissionModal" class="modal fade" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="createPermissionModalLabel">Create Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="createPermissionName" class="form-label">Permission Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="createPermissionName" 
                            name="name" 
                            placeholder="Enter permission name" 
                            required
                        >
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Permission Modal -->
<div id="editPermissionModal" class="modal fade" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="editPermissionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="editPermissionName" class="form-label">Permission Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="editPermissionName" 
                            name="name" 
                            required
                        >
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Open Edit Modal and Populate Data
    function openEditModal(id, name) {
        const form = document.getElementById('editPermissionForm');
        form.action = `/permissions/${id}`; // Update this route if necessary
        document.getElementById('editPermissionName').value = name;

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editPermissionModal'));
        editModal.show();
    }

    // Confirm Delete
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        const confirmation = confirm("Are you sure you want to delete this permission?");
        if (confirmation) {
            form.submit();
        }
    }
</script>
@endsection
