@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>
    <h2>Statements</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div> <!-- Empty div to keep alignment -->
        <div>
            @role('admin')
            @if($project->statements->isEmpty())
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCsvModal">
                <i class="fas fa-file-upload"></i> Upload Statements CSV
            </button>
            @endif
            @endrole
            <button type="button" id="export-csv" class="btn btn-sm btn-success me-2">
                <i class="fas fa-file-download"></i> Download CSV
            </button>
        </div>
    </div>
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
                    <span style="color: {{ $statement->status == \App\Models\Statement::STATUS_APPROVED ? '#28a745' : ($statement->status == \App\Models\Statement::STATUS_PENDING ? '#ffc107' : '#dc3545') }}">
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

<!-- Modal for Uploading CSV -->
<div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCsvModalLabel">Upload CSV to Add Statements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('projects.uploadstatementcsv', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

<script>
    $(document).ready(function() {
        $('#statements-table').DataTable();

        // CSV Export Functionality
        $('#export-csv').click(function() {
            let csv = [];
            const rows = document.querySelectorAll("#statements-table tr");

            rows.forEach(row => {
                const cols = row.querySelectorAll("td, th");
                const rowData = [];
                cols.forEach(col => rowData.push(col.innerText.trim()));
                csv.push(rowData.join(","));
            });

            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "{{ $project->name }}_statements.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Evidence Upload
        document.querySelectorAll('.submit-evidence').forEach(button => {
            button.addEventListener('click', function() {
                const statementId = this.getAttribute('data-statement-id');
                const form = document.querySelector(`#upload-evidence-form-${statementId}`);
                const messageDiv = document.querySelector(`#evidence-message-${statementId}`);

                const formData = new FormData(form);
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

        // Add Comments
        document.querySelectorAll('.add-comment').forEach(button => {
            button.addEventListener('click', function() {
                const statementId = this.getAttribute('data-statement-id');
                const commentContent = prompt('Enter your comment:');
                if (commentContent) {
                    fetch(`/statements/${statementId}/comments`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'text/plain',
                            },
                            body: commentContent,
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
                                location.reload();
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