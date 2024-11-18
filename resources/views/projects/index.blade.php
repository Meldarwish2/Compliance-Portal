@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Projects</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    @role('admin')
                    <th>Assigned Users</th>
                    <th>Statements</th>
                    <th>Evidence</th>
                    @endrole
                    @role('auditor')
                    <th>Statements</th>
                    <th>Evidence</th>
                    @endrole
                    @role('client')
                    <th>Statements</th>
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
                        
                        @role('admin')
                        <td>
                                <ul>
                                    @foreach ($project->users as $user)
                                        <li>{{ $user->name }} ({{ $user->getRoleNames()->first() }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @foreach ($project->statements as $statement)
                                        <li>{{ $statement->content }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @foreach ($project->evidences as $evidence)
                                    <a href="{{ route('evidences.index', $evidence->id) }}" class="btn btn-info btn-sm">Download Evidence</a><br>
                                @endforeach
                            </td>
                            @endrole
                            
                            @role('auditor')
                            <td>
                                <ul>
                                    @foreach ($project->statements as $statement)
                                        <li>{{ $statement->content }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @foreach ($project->evidences as $evidence)
                                    <a href="{{ route('evidences.index', $evidence->id) }}" class="btn btn-info btn-sm">Download Evidence</a><br>
                                @endforeach
                            </td>
                            @endrole
                            
                            @role('client')
                            <td>
                                <ul>
                                    @foreach ($project->statements as $statement)
                                        <li>{{ $statement->content }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <form action="{{ route('evidences.upload', $project->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <input type="file" name="evidence" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            </td>
                            @endrole
                            <td>
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-info">View</a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
