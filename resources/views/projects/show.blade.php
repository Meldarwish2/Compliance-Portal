@extends('layouts.master2')

<style>
    .rate {
        border-bottom-right-radius: 12px;
        border-bottom-left-radius: 12px
    }

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center
    }

    .rating>input {
        display: none
    }

    .rating>label {
        position: relative;
        width: 1em;
        font-size: 30px;
        font-weight: 300;
        color: #FFD600;
        cursor: pointer
    }

    .rating>label::before {
        content: "\2605";
        position: absolute;
        opacity: 0
    }

    .rating>label:hover:before,
    .rating>label:hover~label:before {
        opacity: 1 !important
    }

    .rating>input:checked~label:before {
        opacity: 1
    }

    .rating:hover>input:checked~label:before {
        opacity: 0.4
    }

    .buttons {
        top: 36px;
        position: relative
    }

    .rating-submit {
        border-radius: 8px;
        color: #fff;
        height: auto
    }

    .rating-submit:hover {
        color: #fff
    }
</style>

@section('content')
<div class="container">
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Project Chart</h5>
                </div>
                <div class="card-body">
                    <div id="project-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Project Chart</h5>
                </div>
                <div class="card-body">
                    <div id="project-chart2"></div>
                </div>
            </div>
        </div>
    </div>
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


    $(document).ready(function() {
        // When a rating is selected
        $('input[name="rating"]').on('change', function() {
            // Get the selected rating value
            var rating = $(this).val();

            // Get the evidence ID from the form's data-evidence-id attribute
            var evidenceId = $('#rating-form').data('evidence-id');

            // Send the AJAX request to update the rating
            $.ajax({

                url: '{{ route("evidences.rate", ":id") }}'.replace(':id', evidenceId),

                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    rating: rating,
                    evidence_id: evidenceId // Send the evidence ID
                },
                success: function(response) {
                    // Handle the success response
                    alert('Rating submitted successfully!');
                    console.log(response);
                    // Reload the DataTable 
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    alert('There was an error submitting your rating.');
                }
            });
        });
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

    function generateCsvFromTable(tableSelector) {
        const rows = [];
        const table = document.querySelector(tableSelector);

        if (!table) {
            console.error(`Table with selector "${tableSelector}" not found.`);
            return '';
        }

        // Get table headers
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        rows.push(headers.join(',')); // Add headers to the CSV

        // Get table rows
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td')).map(td => {
                // Remove commas from data to prevent CSV corruption
                return `"${td.textContent.trim().replace(/"/g, '""')}"`;
            });
            rows.push(cells.join(',')); // Add row to the CSV
        });

        return rows.join('\n'); // Combine all rows into a CSV string
    }

    function downloadCsv(csvContent, filename) {
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');

        // Check for browser support
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename); // For IE10+
        } else {
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
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
                console.log('Server response:', data);
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    form.reset();
                    if (data.data && Array.isArray(data.data)) {
                        table.clear().rows.add(data.data).draw(); // Update the table with new data
                    }
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
    }

    function setupStarRating(element) {
        const stars = element.find('.star');

        // Handle mouseover to highlight stars
        stars.on('mouseover', function() {
            const index = $(this).data('rating') - 1; // Using data-rating instead of index()
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
            const index = $(this).data('rating') - 1; // Using data-rating instead of index()
            stars.each(function(i) {
                $(this).toggleClass('selected', i <= index);
            });

            // Optionally, store the selected value in a hidden input or data attribute
            const ratingValue = index + 1;
            element.find('input.rating-value').val(ratingValue);
        });

        // Set initial rating (if exists)
        const initialRating = element.find('input.rating-value').val();
        if (initialRating) {
            stars.each(function(i) {
                $(this).toggleClass('selected', i < initialRating);
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
<script>
    var projectType = "{{ $project->type }}"; // Get the project type from the backend
    var data = {!!json_encode($data)!!};

    var options;
    var series, labels, colors;

    // Prepare data and configurations based on the project type
    switch (projectType) {
        case 'rating':
            series = [
                ...data.ratings, // Ratings 1-5
                data.statuses.reject, // Reject
                data.statuses.pending, // Pending
                data.statuses.assigned_to_qa // Assigned to QA
            ];
            labels = [
                'Rating 1', 'Rating 2', 'Rating 3', 'Rating 4', 'Rating 5',
                'Reject', 'Pending', 'Assigned to QA'
            ];
            colors = [
                '#008000', '#00CC00', '#33FF33', '#66FF66', '#99FF99',
                '#FF0000', '#FFA500', '#0000FF'
            ];
            break;

        case 'accept_reject':
            series = [
                data.statuses.approved, // Approved
                data.statuses.rejected, // Rejected
                data.statuses.pending, // Pending
            ];
            labels = ['Approved', 'Rejected', 'Pending'];
            colors = ['#008000', '#FF0000', '#FFA500']; // Green and Red
            break;

        case 'compliance':
            series = [
                data.statuses.compliant, // Compliant
                data.statuses.partially_compliant, // Partially Compliant
                data.statuses.rejected, // Rejected
                data.statuses.pending, // Pending
            ];
            labels = ['Compliant', 'Partially Compliant', 'Rejected', 'Pending'];
            colors = ['#008000', '#FFA500', '#FF0000', '#FFA500']; // Green, Orange, Red
            break;
    }

    // Common chart configuration
    var commonOptions = {
        series: series,
        labels: labels,
        colors: colors,
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                return series.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            },
            radialBar: {
                dataLabels: {
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function(w) {
                            return series.reduce((a, b) => a + b, 0);
                        }
                    }
                }
            }
        },
    };

    // Donut Chart Configuration
    var donutOptions = {
        ...commonOptions,
        chart: {
            height: 350,
            type: 'donut',
        }
    };

    var donutChart = new ApexCharts(document.querySelector("#project-chart2"), donutOptions);
    donutChart.render();

    // Radial Chart Configuration
    var radialOptions = {
        ...commonOptions,
        chart: {
            height: 450,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '50%',
                },
                track: {
                    background: '#e0e0e0',
                    strokeWidth: '97%',
                    margin: 5,
                },
                dataLabels: {
                    name: {
                        fontSize: '22px',
                    },
                    value: {
                        fontSize: '16px',
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function(w) {
                            return series.reduce((a, b) => a + b, 0);
                        }
                    }
                }
            }
        },
    };

    var radialChart = new ApexCharts(document.querySelector("#project-chart"), radialOptions);
    radialChart.render();
</script>



@endpush

@push('scripts')
<script>
    function equalizeChartHeights() {
        const chart1 = document.getElementById('project-chart');
        const chart2 = document.getElementById('project-chart2');

        if (chart1 && chart2) {
            // Reset heights to calculate natural heights
            chart1.style.height = 'auto';
            chart2.style.height = 'auto';

            // Get the maximum height between the two
            const maxHeight = Math.max(chart1.offsetHeight, chart2.offsetHeight);

            // Set both charts to the maximum height
            chart1.style.height = `${maxHeight}px`;
            chart2.style.height = `${maxHeight}px`;
        }
    }

    // Equalize heights on document load and window resize
    document.addEventListener('DOMContentLoaded', equalizeChartHeights);
    window.addEventListener('resize', equalizeChartHeights);
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

    /* Ensure both chart containers are flexible */
    #project-chart,
    #project-chart2 {
        width: 100%;
        /* Full width of the card */
        min-height: 400px;
        /* Optional: Set a minimum height */
    }

    /* Ensure the parent row and columns stretch */
    .row {
        display: flex;
        flex-wrap: wrap;
        /* Prevent overflow on small screens */
    }

    .col-lg-6 {
        display: flex;
        flex-direction: column;
        /* Ensure child elements stack */
    }

    .card-body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        /* Stretch to match the card */
    }
</style>
@endpush
@endsection