@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Assigned Projects</h1>
        @if($projects->isEmpty())
            <div class="alert alert-info">You have no assigned projects at the moment.</div>
        @else
            @foreach($projects as $project)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3>{{ $project->name }}</h3>
                        <p>Status:
                            <span style="color: {{ $project->status == 'completed' ? '#28a745' : ($project->status == 'Pending' ? '#ffc107' : '#6c757d') }}">
                    {{ ucfirst($project->status) }}
                </span>
                        </p>
                    </div>
                    <div class="card-body">
                        <p>{{ $project->description }}</p>
                        <h5>Statements</h5>
                        @if($project->statements->isEmpty())
                            <div class="alert alert-info">No statements available for this project.</div>
                        @else
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Client Comment</th>
                                    <th>Auditor Comment</th>
                                    <th>Evidence</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($project->statements as $statement)
                                    <tr>
                                        <td>
                            <span style="color: {{ $statement->status == 'approved' ? '#28a745' : ($statement->status == 'Pending' ? '#dc3545' : '#ffc107') }}">
                                {{ ucfirst($statement->status) }}
                            </span>
                                        </td>
                                        <td>
                                            {{ $statement->creator_role === 'client' ? $statement->content : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $statement->creator_role === 'auditor' ? $statement->content : 'N/A' }}
                                        </td>
                                        <td>
                                            @foreach ($project->evidences as $evidence)
                                                <a href="{{ route('evidences.download', $evidence->id) }}" class="btn btn-info btn-sm mb-1">Download Evidence</a><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- Client Role: Upload Evidence --}}
                        @role('client')
                        <form id="upload-evidence-form-{{ $project->id }}" class="mt-3" enctype="multipart/form-data">
                            @csrf
                            <h6>Upload Evidence for this Project</h6>
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <div class="form-group">
                                <div class="dropzone" id="dropzone-{{ $project->id }}">
                                    <p>Drag & Drop files here or click to upload</p>
                                    <input type="file" name="evidence" class="form-control" required style="display: none;">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm submit-evidence" data-project-id="{{ $project->id }}">Upload Evidence</button>
                            <div id="evidence-message-{{ $project->id }}" class="mt-2"></div>
                        </form>
                        @endrole
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dropzone').forEach(dropzone => {
                dropzone.addEventListener('click', function () {
                    this.querySelector('input[type="file"]').click();
                });

                dropzone.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                });

                dropzone.addEventListener('dragleave', function () {
                    this.classList.remove('dragover');
                });

                dropzone.addEventListener('drop', function (e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                    const fileInput = this.querySelector('input[type="file"]');
                    fileInput.files = e.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change'));
                });
            });

            document.querySelectorAll('.submit-evidence').forEach(button => {
                button.addEventListener('click', function () {
                    const projectId = this.getAttribute('data-project-id');
                    const form = document.querySelector(`#upload-evidence-form-${projectId}`);
                    const messageDiv = document.querySelector(`#evidence-message-${projectId}`);

                    // Prepare form data
                    const formData = new FormData(form);

                    // AJAX request for evidence upload
                    fetch(`{{ url('/projects') }}/${projectId}/evidences/upload`, {
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
                                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                                form.reset();
                            } else {
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

    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }

        .dropzone.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
    </style>
@endsection
