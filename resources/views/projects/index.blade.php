@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Projects</h1>
    @role('admin')
    <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
    @endrole
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                @role('admin')
                <th>Assigned Users</th>
                @endrole
                <th>Client Statements</th>
                <th>Auditor Statements</th>
                @role('admin|auditor')
                <th>Evidence</th>
                @endrole
                @role('client')
                <th>Upload Evidence</th>
                @endrole
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td>{{ $project->name }}</td>
                <td>{{ $project->description }}</td>
                <td>{{ $project->status }}</td>

                {{-- Assigned Users (Admin Only) --}}
                @role('admin')
                <td>
                    <ul>
                        @foreach ($project->users as $user)
                        <li>{{ $user->name }} ({{ $user->getRoleNames()->first() }})</li>
                        @endforeach
                    </ul>
                </td>
                @endrole

                {{-- Client Statements --}}
                <td>
                    <ul id="statements-list-{{ $project->id }}">
                        @foreach ($project->statements->where('creator_role', 'client') as $statement)
                        <li>{{ $statement->content }}</li>
                        @endforeach
                    </ul>

                    @role('client')
                    {{-- Add Statement Form for Client --}}
                    <form id="statement-form-{{ $project->id }}" class="mt-2">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="form-group">
                            <input type="text" name="content" class="form-control" placeholder="Add a statement" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm submit-statement" data-project-id="{{ $project->id }}">
                            Add Statement
                        </button>
                    </form>
                    <div id="statement-message-{{ $project->id }}" class="mt-2"></div>
                    @endrole
                </td>

                {{-- Auditor Statements --}}
                <td>
                    <ul>
                        @foreach ($project->statements->where('creator_role', 'auditor') as $statement)
                        <li>{{ $statement->content }}</li>
                        @endforeach
                    </ul>
                    {{-- Auditor Role: Add Statement Form --}}
                    @role('auditor')
                    <form id="statement-form-{{ $project->id }}" class="mt-2">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="form-group">
                            <input type="text" name="content" class="form-control" placeholder="Add a statement" required>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm submit-statement" data-project-id="{{ $project->id }}">
                            Add Statement
                        </button>
                    </form>
                    <div id="statement-message-{{ $project->id }}" class="mt-2"></div>
                    @endrole
                </td>

                {{-- Evidence (Admin and Auditor) --}}
                @role('admin|auditor')
                <td>
                    @foreach ($project->evidences as $evidence)
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="btn btn-info btn-sm mb-1">Download Evidence</a><br>
                    @endforeach
                </td>
                @endrole

                {{-- Upload Evidence (Client Only) --}}
                @role('client')
                <td>
                    <form id="upload-evidence-form-{{ $project->id }}" class="mt-2" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="form-group">
                            <input type="file" name="evidence" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm submit-evidence" data-project-id="{{ $project->id }}">
                            Upload Evidence
                        </button>
                    </form>
                    <div id="evidence-message-{{ $project->id }}" class="mt-2"></div>
                </td>
                @endrole

                {{-- Actions --}}
                <td>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-info btn-sm">View</a>
                    @role('admin')
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    @endrole
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Statement submission (for client and auditor)
        document.querySelectorAll('.submit-statement').forEach(function(button) {
            button.addEventListener('click', function() {
                const projectId = this.getAttribute('data-project-id');
                const form = document.querySelector(`#statement-form-${projectId}`);
                const messageDiv = document.querySelector(`#statement-message-${projectId}`);
                const statementList = document.querySelector(`#statements-list-${projectId}`);

                // Prepare form data
                const formData = new FormData(form);

                // Send AJAX request
                fetch('{{ route('statements.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Append the new statement to the list
                        const li = document.createElement('li');
                        li.textContent = data.statement.content;
                        statementList.appendChild(li);

                        // Show success message
                        messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        form.reset();
                    } else {
                        // Show error message
                        messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
                });
            });
        });

        // Handle Evidence upload via AJAX (for client only)
        document.querySelectorAll('.submit-evidence').forEach(function(button) {
            button.addEventListener('click', function() {
                const projectId = this.getAttribute('data-project-id');
                const form = document.querySelector(`#upload-evidence-form-${projectId}`);
                const messageDiv = document.querySelector(`#evidence-message-${projectId}`);

                // Prepare form data
                const formData = new FormData(form);

                // Send AJAX request for file upload
                fetch('{{ route('evidences.upload', ['project' => '__PROJECT_ID__']) }}'.replace('__PROJECT_ID__', projectId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        form.reset();
                    } else {
                        // Show error message
                        messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
                });
            });
        });
    });
</script>

@endsection
