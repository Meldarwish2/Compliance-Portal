@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Permission: {{ $permission->name }}</h2>

    <p>Created at: {{ $permission->created_at }}</p>
    <p>Updated at: {{ $permission->updated_at }}</p>

    <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">Back to Permissions</a>
</div>
@endsection
