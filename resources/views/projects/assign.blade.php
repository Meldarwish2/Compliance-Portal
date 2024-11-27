@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light.bg-gradient text-white">
                <h1 class="mb-0">Manage Users for Project: <span class="fw-bold">{{ $project->name }}</span></h1>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Assign User Section -->
                    <div class="col-md-6">
                        <h2 class="text-primary">Assign User</h2>
                        <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-group mb-3">
                                <label for="user_id" class="form-label">Select User</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="" disabled selected>Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a user to assign.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Assign User</button>
                        </form>
                    </div>

                    <!-- Revoke User Section -->
                    <div class="col-md-6">
                        <h2 class="text-danger">Revoke User</h2>
                        <form action="{{ route('projects.revokeAccess', [$project->id, 'user_id']) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-group mb-3">
                                <label for="revoke_user_id" class="form-label">Select User</label>
                                <select name="user_id" id="revoke_user_id" id = user_id class="form-select" required>
                                    <option value="" disabled selected>Select a user</option>
                                    @foreach($project->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a user to revoke access.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Revoke Access</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enable Bootstrap validation
        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection
