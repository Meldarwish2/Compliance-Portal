@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Assign/Revoke Users to/from Project: {{ $project->name }}</h1>

        <h2>Assign User</h2>
        <form action="{{ route('projects.assign', $project->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select name="user_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign User</button>
        </form>

        <h2 class="mt-4">Revoke User</h2>
        <form action="{{ route('projects.revokeAccess', [$project->id, 'user_id']) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select name="user_id" class="form-control" required>
                    @foreach($project->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-danger">Revoke Access</button>
        </form>
    </div>
@endsection