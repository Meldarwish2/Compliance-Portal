@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Audit Logs</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Event</th>
                <th>User</th>
                <th>Changes</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audits as $audit)
            <tr>
                <td>{{ $audit->event }}</td>
                <td>{{ $audit->user->name ?? 'System' }}</td>
                <td>
                    <ul>
                        @foreach($audit->getModified() as $attribute => $modified)
                        <li>
                            <strong>{{ $attribute }}</strong>: 
                            <span style="color: red;">{{ isset($modified['old']) ? json_encode($modified['old']) : 'N/A' }}</span> → 
                            <span style="color: green;">{{ isset($modified['new']) ? json_encode($modified['new']) : 'N/A' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $audit->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
