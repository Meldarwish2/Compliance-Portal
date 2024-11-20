@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Audit Logs</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Event</th>
                <th>User</th>
                <th>Attribute</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audits as $audit)
            @foreach($audit->getModified() as $attribute => $modified)
            <tr>
                <td>{{ $audit->event }}</td>
                <td>{{ $audit->user->name ?? 'System' }}</td>
                <td>{{ $attribute }}</td>
                <td style="color: red;">{{ isset($modified['old']) ? json_encode($modified['old']) : 'N/A' }}</td>
                <td style="color: green;">{{ isset($modified['new']) ? json_encode($modified['new']) : 'N/A' }}</td>
                <td>{{ $audit->created_at }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection