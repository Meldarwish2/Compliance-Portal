@extends('layouts.master2')

@section('content')
<div id="loadingOverlay" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-white">Loading...</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <h1>Projects</h1>

    {{-- Admin-only: Create Project Button --}}
    @role('admin')
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">Create New Project</button>
    </div>
    @endrole

    @if($projects->isEmpty())
    <div class="alert alert-info">You have no projects at the moment.</div>
    @else
    <table id="model-datatables" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Description</th>
                <th>Compliance Framework</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects->whereNotNull('parent_project_id') as $project)
            <tr>
                <td>{{ $project->name }}</td>
                <td>{{ $project->description }}</td>
                <td>{{ $project->parent->name ?? '—' }}</td>
                <td>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('projects.show', $project->id) }}" class="dropdown-item">
                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View Details
                            </a>
                            @role('admin')
                            <a href="{{ route('projects.assignUsers', $project->id) }}" class="dropdown-item">
                                <i class="ri-user-add-fill align-bottom me-2 text-muted"></i> Assign/Revoke Users
                            </a>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteProjectModal{{ $project->id }}">
                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                            </button>
                            @endrole
                        </ul>
                    </div>
                </td>
            </tr>

            {{-- Delete Project Modal --}}
            <div class="modal fade" id="deleteProjectModal{{ $project->id }}" tabindex="-1" aria-labelledby="deleteProjectModalLabel{{ $project->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteProjectModalLabel{{ $project->id }}">Delete Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete the project "{{ $project->name }}"?</p>
                            <p>Choose an option:</p>
                            <div class="form-check">
                                <input class="form-check-input delete-option" type="radio" name="deleteOption{{ $project->id }}" id="deleteOptionDelete{{ $project->id }}" value="delete" checked>
                                <label class="form-check-label" for="deleteOptionDelete{{ $project->id }}">
                                    Delete project and all related files
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input delete-option" type="radio" name="deleteOption{{ $project->id }}" id="deleteOptionArchive{{ $project->id }}" value="archive">
                                <label class="form-check-label" for="deleteOptionArchive{{ $project->id }}">
                                    Delete project but encrypt and archive all related files
                                </label>
                            </div>
                            <div id="passwordInputContainer{{ $project->id }}" class="mt-3" style="display:none;">
                                <label for="zipPassword{{ $project->id }}">ZIP Password:</label>
                                <input type="password" class="form-control" id="zipPassword{{ $project->id }}" name="zip_password" placeholder="Enter ZIP password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" id="deleteProjectForm{{ $project->id }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_option" id="deleteOptionInput{{ $project->id }}" value="delete">
                                <input type="hidden" name="zip_password" id="zipPasswordInput{{ $project->id }}" value="">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Create Project Modal --}}
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProjectModalLabel">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="createProjectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="parent_project" class="form-label">Compliance Framework</label>
                        <select name="parent_project_id" id="parent_project" class="form-control">
                            <option value="">None (Create as Compliance Framework)</option>
                            @foreach($projects->whereNull('parent_project_id') as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_project')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Client Name Input Field --}}
                    <div class="mb-3" id="clientNameField" style="display: none;">
                        <label for="clientName" class="form-label">Client Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="clientName" name="client_name" required>
                        @error('client_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Project Name Input Field --}}
                    <div class="mb-3" id="projectNameField">
                        <label for="projectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="projectName" name="name" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required></textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="csvUploadField">
                        <label for="csvFile" class="form-label">Upload CSV File</label>
                        <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csvFile" name="csv_file" accept=".csv">
                        @error('csv_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to show the loading spinner modal
    function showLoadingSpinner() {
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingOverlay'));
        loadingModal.show();
    }

    // Show spinner when the "Create Project" form is submitted
    document.getElementById('createProjectForm').addEventListener('submit', function(e) {
        showLoadingSpinner();
    });

    // Show spinner when the "Delete Project" form is submitted
    // @foreach($projects as $project)
    // document.getElementById('deleteProjectForm{{ $project->id }}').addEventListener('submit', function(e) {
    //     showLoadingSpinner();
    // });
    // @endforeach

    document.getElementById('parent_project').addEventListener('change', function() {
        const clientNameField = document.getElementById('clientNameField');
        const projectNameField = document.getElementById('projectNameField');
        const csvUploadField = document.getElementById('csvUploadField');
        const csvFileInput = document.getElementById('csvFile');
        const projectNameInput = document.getElementById('projectName');
        const clientNameInput = document.getElementById('clientName');

        if (this.value) {
            clientNameField.style.display = 'block';
            projectNameField.style.display = 'none';
            csvUploadField.style.display = 'none';
            csvFileInput.value = "";
            csvFileInput.removeAttribute('required');

            clientNameInput.addEventListener('input', function() {
                const complianceFramework = document.querySelector('#parent_project option:checked').text;
                projectNameInput.value = `${clientNameInput.value}_${complianceFramework}`;
            });
        } else {
            clientNameField.style.display = 'none';
            projectNameField.style.display = 'block';
            csvUploadField.style.display = 'block';
            csvFileInput.setAttribute('required', 'required');
        }
    });
</script>

@push('styles')
<style>
    #loadingOverlay {
        background: rgba(0, 0, 0, 0.75); /* Semi-transparent black background */
    }
</style>
@endpush
@endsection
