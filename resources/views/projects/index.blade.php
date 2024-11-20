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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->description }}</td>
                        <td>
                            <span style="color: {{ $project->status == 'completed' ? '#28a745' : ($project->status == 'Pending' ? '#ffc107' : '#6c757d') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm">View Details</a>
                            {{-- Admin-only: Edit Project Button --}}
                            @role('admin')
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="{{ route('projects.assignUsers', $project->id) }}" class="btn btn-info btn-sm">Assign/Revoke Users</a>
                            @endrole
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection