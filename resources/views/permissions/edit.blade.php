@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Edit Permission - {{ $permission->name }}</h2>

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Permission Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $permission->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Permission</button>
    </form>
</div>
@endsection
