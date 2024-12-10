@extends('layouts.master2')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <!-- <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Create User</a> -->
            <div class="card-header">
                <!-- <h5 class="card-title mb-0">Modal Data Datatables</h5> -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#topmodal">Create User</button>
            </div>

            <div class="card-body">
                <table id="model-datatables" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)

                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                            <td>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);"
                                            class="dropdown-item edit-item-btn"
                                            onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->roles->pluck('id') }})">
                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                        </a>

                                        <li>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event)">
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
        </div>
    </div>
</div>

<div id="topmodal" class="modal fade" tabindex="-1" aria-labelledby="topmodalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="topmodalLabel">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="error-summary">
                    <ul class="mb-0"></ul>
                </div>

                <form id="createUserForm" action="{{ route('users.store') }}" method="POST" novalidate>
                    @csrf
                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            placeholder="Enter the user's name"
                            value="{{ old('name') }}"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            placeholder="Enter the user's email"
                            value="{{ old('email') }}"
                            required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Enter a secure password"
                            required>
                        <small class="form-text text-muted">Must be at least 8 characters.</small>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Re-enter the password"
                            required>
                    </div>

                    <!-- Role -->
                    <div class="form-group mb-4">
                        <label for="role" class="form-label">Role</label>
                        <select
                            name="role"
                            id="role"
                            class="form-control @error('role') is-invalid @enderror"
                            required>
                            <option value="" disabled selected>Select a role</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="editUserModal" class="modal fade" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editName"
                            name="name"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            id="editEmail"
                            name="email"
                            required>
                    </div>

                    <!-- Role -->
                    <div class="form-group mb-4">
                        <label for="editRole" class="form-label">Role</label>
                        <select
                            name="role"
                            id="editRole"
                            class="form-control"
                            required>
                            <option value="" disabled>Select a role</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Disable button and show spinner during form submission
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.querySelector('.spinner-border').classList.remove('d-none');
    });

    // Client-side validation summary
    const form = document.getElementById('createUserForm');
    form.addEventListener('submit', (e) => {
        const invalidInputs = form.querySelectorAll(':invalid');
        const errorSummary = document.getElementById('error-summary');
        if (invalidInputs.length > 0) {
            e.preventDefault();
            errorSummary.classList.remove('d-none');
            const errorList = errorSummary.querySelector('ul');
            errorList.innerHTML = '';
            invalidInputs.forEach(input => {
                const label = form.querySelector(`label[for="${input.id}"]`).innerText;
                errorList.innerHTML += `<li>${label} is required.</li>`;
            });
        }
    });
</script>
<script>
    function openEditModal(id, name, email, roles) {
        // Populate the form with user data
        const form = document.getElementById('editUserForm');
        form.action = `/users/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;

        // Pre-select roles
        const roleSelect = document.getElementById('editRole');
        Array.from(roleSelect.options).forEach(option => {
            option.selected = roles.includes(parseInt(option.value));
        });

        // Show the modal
        const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editUserModal.show();
    }
</script>

<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        const confirmation = confirm("Are you sure you want to delete this user?");
        if (confirmation) {
            form.submit();
        }
    }
</script>


@endsection