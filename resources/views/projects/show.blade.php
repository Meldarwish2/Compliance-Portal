@extends('layouts.master2')

@section('content')
<div class="container">
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>

    <h2>Statements</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div> <!-- Alignment spacer -->
        <div>
            @role('admin')
            @if($project->statements->isEmpty())
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCsvModal">
                <i class="fas fa-file-upload"></i> Upload Statements CSV
            </button>
            @endif
            @endrole
            <button id="export-csv" class="btn btn-sm btn-success">
                <i class="fas fa-file-download"></i> Download CSV
            </button>
        </div>
    </div>

    <table id="statements-table" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
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
                <td>
                    <!-- Button to trigger the modal -->
                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#statementModal{{ $statement->id }}">
                        View Full Statement
                    </button>

                    <!-- Modal for displaying the full statement content -->
                    <div class="modal fade" id="statementModal{{ $statement->id }}" tabindex="-1" role="dialog" aria-labelledby="statementModalLabel{{ $statement->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="statementModalLabel{{ $statement->id }}">Statement Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @php
                                    $content = json_decode($statement->content, true);
                                    @endphp

                                    <h6 class="fs-15">Statement Content</h6>

                                    @if(!empty($content))
                                    @foreach($content as $key => $value)
                                    <div class="d-flex mt-2">
                                        <div class="flex-shrink-0">
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="text-muted mb-0">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <p class="text-muted">No content available.</p>
                                    @endif
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </td>

                <td>
                    <span style="color: {{ $statement->getStatusColor() }}">
                        {{ ucfirst($statement->status) }}
                    </span>
                </td>
                <td class="client-comments">
                    @foreach($statement->getClientComments() as $comment)
                    <div>{{ $comment->content }}</div>
                    @endforeach
                </td>
                <td class="auditor-comments">
                    @foreach($statement->getAuditorComments() as $comment)
                    <div>{{ $comment->content }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach($statement->evidences as $evidence)
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="btn btn-info btn-sm mb-1">{{ $evidence->file_name }}</a><br>
                    @endforeach
                </td>
                <td>
                    @include('partials.statement-actions', ['statement' => $statement, 'project' => $project])
                </td>
            </tr>

            @include('partials.add-comment-modal', ['statement' => $statement])
            @endforeach
        </tbody>
    </table>
</div>

@include('partials.upload-csv-modal', ['project' => $project])

@push('scripts')
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
    $(document).ready(function() {
        initializeDataTable('#statements-table');
        initializeCsvExport('#export-csv', '{{ $project->name }}');
        initializeEvidenceUpload();
        initializeCommentSubmission();
        initializeStarRating();
    });

    function initializeDataTable(selector) {
        return $(selector).DataTable({
            responsive: false,
            columns: [{
                    data: 'content',
                    title: 'Statement',
                },
                {
                    data: 'status',
                    title: 'Status'
                },
                {
                    data: 'client_comments',
                    title: 'Client Comments'
                },
                {
                    data: 'auditor_comments',
                    title: 'Auditor Comments'
                },
                {
                    data: 'evidence',
                    title: 'Evidence'
                },
                {
                    data: 'actions',
                    title: 'Actions'
                },
            ],
        });
    }

    function initializeCsvExport(buttonId, filename) {
        $(buttonId).on('click', function() {
            const csv = generateCsvFromTable('#statements-table');
            downloadCsv(csv, `${filename}_statements.csv`);
        });
    }

    function initializeEvidenceUpload() {
        $('.submit-evidence').on('click', function() {
            const statementId = $(this).data('statement-id');
            submitEvidence(statementId);
        });
    }

    function submitEvidence(statementId) {
        const form = document.querySelector(`#upload-evidence-form-${statementId}`);
        const messageDiv = document.querySelector(`#evidence-message-${statementId}`);
        const table = $('#statements-table').DataTable();

        const formData = new FormData(form);
        fetch(`{{ url('/statements') }}/${statementId}/evidences/upload`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    form.reset();
                    table.ajax.reload(null, false); // Reload without resetting pagination
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
            });
    }

    function initializeCommentSubmission() {
        $('.submit-comment').on('click', function() {
            const statementId = $(this).data('statement-id');
            submitComment(statementId);
        });
    }

    function initializeStarRating() {
        $('.rating').each(function() {
            setupStarRating($(this));
        });

        function setupStarRating(element) {
            const stars = element.find('.star');

            // Handle mouseover to highlight stars
            stars.on('mouseover', function() {
                const index = $(this).index();
                stars.each(function(i) {
                    $(this).toggleClass('hover', i <= index);
                });
            });

            // Handle mouseleave to remove highlights
            stars.on('mouseleave', function() {
                stars.removeClass('hover');
            });

            // Handle click to select stars
            stars.on('click', function() {
                const index = $(this).index();
                stars.each(function(i) {
                    $(this).toggleClass('selected', i <= index);
                });

                // Optionally, store the selected value in a hidden input or data attribute
                const ratingValue = index + 1;
                element.find('input.rating-value').val(ratingValue);
            });
        }
    }

    function renderEvidence(data) {
        return data.map(evidence =>
            `<a href="{{ url('/evidence/download') }}/${evidence.id}" class="btn btn-info btn-sm">${evidence.file_name}</a>`
        ).join('<br>');
    }

    function renderActions(data) {
        return `<a href="#" class="btn btn-primary">Edit</a>`;
    }
</script>
@endpush

@push('styles')
<style>
    .rating .star {
        cursor: pointer;
        font-size: 1.5rem;
        color: #ccc;
    }

    .rating .star.hover,
    .rating .star.selected {
        color: #ffcc00;
    }
</style>
@endpush
@endsection