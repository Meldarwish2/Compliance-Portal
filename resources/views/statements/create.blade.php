@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Add Statement</h1>
        <form action="{{ route('statements.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" class="form-control" required></textarea>
            </div>
            <input type="hidden" name="project_id" value="{{ $project_id }}">
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
@endsection
