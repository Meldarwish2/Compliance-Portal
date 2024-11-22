@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>
    <h2>Statements</h2>
    <table class="table" id="statements-table">
        <thead>
            <tr>
                <th>Statement</th>
                <th>Status</th>
                <th>Client Comments</th>
                <th>Auditor Comments</th>
                <th>Evidence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->statements as $statement)
            <tr>
                <td>{{ $statement->content }}</td>
                <td>
                    <span style="color: {{ $statement->status == \App\Models\Statement::STATUS_APPROVED ? '#28a745' : ($statement->status ==  \App\Models\Statement::STATUS_PENDING ? '#ffc107' : '#dc3545') }}">
                        {{ ucfirst($statement->status) }}
                    </span>
                </td>
                <td>
                    @foreach($statement->comments->where('role', 'client') as $comment)
                    <div>{{ $comment->content }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach($statement->comments->where('role', 'auditor') as $comment)
                    <div>{{ $comment->content }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach($statement->evidences as $evidence)
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="btn btn-info btn-sm mb-1">{{$evidence->file_name}}</a><br>
                    @endforeach
                </td>
                <td>
                    {{-- Client Role: Upload Evidence --}}
                    @role('client')
                    <form id="upload-evidence-form-{{ $statement->id }}" class="mt-3" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="statement_id" value="{{ $statement->id }}">
                        <div class="form-group">
                            <input type="file" name="evidence" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm submit-evidence" data-statement-id="{{ $statement->id }}">Upload Evidence</button>
                        <div id="evidence-message-{{ $statement->id }}" class="mt-2"></div>
                    </form>
                    @endrole

                    {{-- Auditor Role: Download Evidence, Approve, Reject --}}
                    @role('auditor')
                    @foreach($statement->evidences as $evidence)
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="btn btn-info btn-sm mb-1">Download Evidence</a><br>
                    <form action="{{ route('evidences.approve', $evidence->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form action="{{ route('evidences.reject', $evidence->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                    @endforeach
                    @endrole

                    {{-- Add Comment Button --}}
                    <button type="button" class="btn btn-secondary btn-sm add-comment" data-statement-id="{{ $statement->id }}">Add Comment</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#statements-table').DataTable();
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submit-evidence').forEach(button => {
            button.addEventListener('click', function() {
                const statementId = this.getAttribute('data-statement-id');
                const form = document.querySelector(`#upload-evidence-form-${statementId}`);
                const messageDiv = document.querySelector(`#evidence-message-${statementId}`);

                // Prepare form data
                const formData = new FormData(form);

                // AJAX request for evidence upload
                fetch(`{{ url('/statements') }}/${statementId}/evidences/upload`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            form.reset();
                        } else {
                            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
                    });
            });
        });

        document.querySelectorAll('.add-comment').forEach(button => {
            button.addEventListener('click', function() {
                const statementId = this.getAttribute('data-statement-id');
                const commentContent = prompt('Enter your comment:');
                if (commentContent) {
                    fetch(`/statements/${statementId}/comments`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel's CSRF token
                                'Accept': 'application/json',
                                'Content-Type': 'text/plain', // Plain text content type
                            },
                            body: commentContent, // Send the raw comment content as the request body
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert('Comment added successfully.');
                                location.reload(); // Reload the page to show the new comment
                            } else {
                                alert('Failed to add comment.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                }
            });
        });

    });
</script>
@endsection