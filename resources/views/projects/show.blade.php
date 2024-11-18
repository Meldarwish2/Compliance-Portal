{{--@extends('layouts.master')--}}

{{--@section('content')--}}
{{--    <div class="container">--}}
{{--        <h1>{{ $project->name }}</h1>--}}
{{--        <p>{{ $project->description }}</p>--}}
{{--        <p>Status: {{ $project->status }}</p>--}}

{{--        <h2>Statements</h2>--}}
{{--        <a href="{{ route('statements.create', ['project_id' => $project->id]) }}" class="btn btn-primary">Add Statement</a>--}}
{{--        <table class="table">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th>Content</th>--}}
{{--                <th>Status</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @foreach($project->statements as $statement)--}}
{{--                <tr>--}}
{{--                    <td>{{ $statement->content }}</td>--}}
{{--                    <td>{{ $statement->status }}</td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
{{--            </tbody>--}}
{{--        </table>--}}

{{--        <h2>Evidences</h2>--}}
{{--        <a href="{{ route('evidences.create', ['project_id' => $project->id]) }}" class="btn btn-primary">Upload Evidence</a>--}}
{{--        <table class="table">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th>File Name</th>--}}
{{--                <th>Status</th>--}}
{{--                <th>Actions</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @foreach($project->evidences as $evidence)--}}
{{--                <tr>--}}
{{--                    <td>{{ $evidence->file_name }}</td>--}}
{{--                    <td>{{ $evidence->status }}</td>--}}
{{--                    <td>--}}
{{--                        @if($evidence->status == 'pending' && auth()->user()->can('approve evidence'))--}}
{{--                            <form action="{{ route('evidences.approve', $evidence) }}" method="POST" style="display:inline">--}}
{{--                                @csrf--}}
{{--                                <button type="submit" class="btn btn-success">Approve</button>--}}
{{--                            </form>--}}
{{--                        @endif--}}
{{--                        @if($evidence->status == 'pending' && auth()->user()->can('reject evidence'))--}}
{{--                            <form action="{{ route('evidences.reject', $evidence) }}" method="POST" style="display:inline">--}}
{{--                                @csrf--}}
{{--                                <button type="submit" class="btn btn-danger">Reject</button>--}}
{{--                            </form>--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
{{--            </tbody>--}}
{{--        </table>--}}

{{--        <h2>Assign Users</h2>--}}
{{--        <form action="{{ route('projects.assign', $project) }}" method="POST">--}}
{{--            @csrf--}}
{{--            <div class="form-group">--}}
{{--                <label for="user_id">Select User</label>--}}
{{--                <select name="user_id" id="user_id" class="form-control">--}}
{{--                    @foreach($users as $user)--}}
{{--                        <option value="{{ $user->id }}">{{ $user->name }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <button type="submit" class="btn btn-primary">Assign</button>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--@endsection--}}

@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>{{ $project->name }}</h1>
        <p>{{ $project->description }}</p>
        <p>Status: {{ $project->status }}</p>

        <h2>Statements</h2>
        <!-- Button to trigger modal for adding statement -->
        <button class="btn btn-primary" data-toggle="modal" data-target="#addStatementModal">Add Statement</button>

        <table class="table mt-3">
            <thead>
            <tr>
                <th>Content</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->statements as $statement)
                <tr>
                    <td>{{ $statement->content }}</td>
                    <td>{{ $statement->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Evidences</h2>
        <!-- Button to trigger modal for uploading evidence -->
        <button class="btn btn-primary" data-toggle="modal" data-target="#uploadEvidenceModal">Upload Evidence</button>

        <table class="table mt-3">
            <thead>
            <tr>
                <th>File Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->evidences as $evidence)
                <tr>
                    <td>{{ $evidence->file_name }}</td>
                    <td>{{ $evidence->status }}</td>
                    <td>
                        @if($evidence->status == 'pending' && auth()->user()->can('approve evidence'))
                            <form action="{{ route('evidences.approve', $evidence) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                        @endif
                        @if($evidence->status == 'pending' && auth()->user()->can('reject evidence'))
                            <form action="{{ route('evidences.reject', $evidence) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Assign Users</h2>
        <form action="{{ route('projects.assign', $project) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select name="user_id" id="user_id" class="form-control">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>

    <!-- Modal for adding a statement -->
    <div class="modal fade" id="addStatementModal" tabindex="-1" role="dialog" aria-labelledby="addStatementModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStatementModalLabel">Add Statement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('statements.create', ['project_id' => $project->id]) }}" method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="content">Statement Content</label>
                            <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Statement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for uploading evidence -->
    <div class="modal fade" id="uploadEvidenceModal" tabindex="-1" role="dialog" aria-labelledby="uploadEvidenceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadEvidenceModalLabel">Upload Evidence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('evidences.create', ['project_id' => $project->id]) }}" method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file_name">File Name</label>
                            <input type="text" name="file_name" id="file_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file">Upload File</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload Evidence</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
