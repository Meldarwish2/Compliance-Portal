@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Project Details</h1>
        <div class="mb-3">
            <strong>Project Name:</strong> {{ $project->name }} <br>
            <strong>Description:</strong> {{ $project->description }} <br>
        </div>

        @role('admin') 
            <h4>Assigned Users</h4>
            <ul>
                @foreach ($project->users as $user)
                    <li>{{ $user->name }} ({{ $user->role }})</li>
                @endforeach
            </ul>

            <h4>Statements</h4>
            @foreach ($project->statements as $statement)
                <p>{{ $statement->content }}</p>
            @endforeach

            <h4>Evidence</h4>
            @foreach ($project->evidences as $evidence)
                <a href="{{ route('evidences.index', $evidence->id) }}" class="btn btn-info">Download Evidence</a>
            @endforeach

            <form action="{{ route('projects.assign', $project->id) }}" method="POST">
                @csrf
                <label for="user">Assign Project to User</label>
                <select name="user" id="user" class="form-control" required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success mt-2">Assign</button>
            </form>
        @endrole

        @role('auditor')
            <h4>Statements</h4>
            @foreach ($project->statements as $statement)
                <p>{{ $statement->content }}</p>
            @endforeach

            <h4>Evidence</h4>
            @foreach ($project->evidences as $evidence)
                <a href="{{ route('evidences.index', $evidence->id) }}" class="btn btn-info">Download Evidence</a>
            @endforeach

            <form action="{{ route('statements.store', $project->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="statement">Add Statement</label>
                    <textarea name="statement" id="statement" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Statement</button>
            </form>
        @endrole

        @role('client')
            <h4>Statements</h4>
            @foreach ($project->statements as $statement)
                <p>{{ $statement->content }}</p>
            @endforeach

            <h4>Upload Evidence</h4>
            <form action="{{ route('evidences.upload', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="evidence">Upload Evidence</label>
                    <input type="file" name="evidence" id="evidence" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>

            <form action="{{ route('statements.store', $project->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="statement">Add Statement</label>
                    <textarea name="statement" id="statement" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Statement</button>
            </form>
        @endrole
    </div>
@endsection
