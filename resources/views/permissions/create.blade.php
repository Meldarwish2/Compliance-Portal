@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Create Permission</h2>

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Permission Name</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Permission</button>
    </form>
</div>
@endsection
