@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Assigned Projects</h1>

        {{-- Admin-only: Create Project Button --}}
        @role('admin')
        <div class="mb-3">
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Create New Project</a>
        </div>
        @endrole

        @if($projects->isEmpty())
            <div class="alert alert-info">You have no assigned projects at the moment.</div>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Description</th>
                    <!-- <th>Status</th> -->
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->description }}</td>
                        <!-- <td>
                            <span style="color: {{ $project->status == 'completed' ? '#28a745' : ($project->status == 'Pending' ? '#ffc107' : '#6c757d') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td> -->
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm">View Details</a>
                            {{-- Admin-only: Edit Project Button --}}
                            @role('admin')
                            <!-- <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-secondary btn-sm">Edit</a> -->
                            <a href="{{ route('projects.assignUsers', $project->id) }}" class="btn btn-info btn-sm">Assign/Revoke Users</a>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProjectModal{{ $project->id }}">Delete</button>
                            @endrole
                        </td>
                    </tr>

                    {{-- Delete Project Modal --}}
                    <div class="modal fade" id="deleteProjectModal{{ $project->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteProjectModalLabel">Delete Project</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete the project "{{ $project->name }}"?</p>
                                    <p>Choose an option:</p>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deleteOption{{ $project->id }}" id="deleteOption1{{ $project->id }}" value="delete" checked>
                                        <label class="form-check-label" for="deleteOption1{{ $project->id }}">
                                            Delete project and all related files
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="deleteOption{{ $project->id }}" id="deleteOption2{{ $project->id }}" value="archive">
                                        <label class="form-check-label" for="deleteOption2{{ $project->id }}">
                                            Delete project but encrypt and archive all related files
                                        </label>
                                    </div>
                                    <div id="passwordInputContainer{{ $project->id }}" style="display:none;">
                                        <label for="zipPassword{{ $project->id }}">ZIP Password:</label>
                                        <input type="password" class="form-control" id="zipPassword{{ $project->id }}" name="zip_password">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            @foreach($projects as $project)
            $('#deleteProjectModal{{ $project->id }} input[type="radio"]').change(function() {
                if ($(this).val() === 'archive') {
                    $('#passwordInputContainer{{ $project->id }}').show();
                } else {
                    $('#passwordInputContainer{{ $project->id }}').hide();
                }
                $('#deleteOptionInput{{ $project->id }}').val($(this).val());
            });

            $('#deleteProjectModal{{ $project->id }}').on('shown.bs.modal', function () {
                $('#zipPassword{{ $project->id }}').val('');
                $('#zipPasswordInput{{ $project->id }}').val('');
            });

            $('#deleteProjectModal{{ $project->id }}').on('hidden.bs.modal', function () {
                $('#zipPassword{{ $project->id }}').val('');
                $('#zipPasswordInput{{ $project->id }}').val('');
            });

            $('#deleteProjectForm{{ $project->id }}').on('submit', function () {
                $('#zipPasswordInput{{ $project->id }}').val($('#zipPassword{{ $project->id }}').val());
            });
            @endforeach
        });
    </script>
@endsection