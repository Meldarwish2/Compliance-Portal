@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Upload Evidence</h1>
        <form action="{{ route('evidences.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file_name">File Name</label>
                <input type="text" name="file_name" id="file_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="file_path">File</label>
                <input type="file" name="file_path" id="file_path" class="form-control-file" required>
            </div>
            <input type="hidden" name="project_id" value="{{ $project_id }}">
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
@endsection
