@extends('layouts.master')
@section('content')
<div class="container-fluid">
    @role('client|auditor')
    <!-- Project Charts Section -->
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-4 project-chart-container">
            <h3 class="text-center">{{ $project->name }}</h3>
            <canvas id="projectCompletionChart-{{ $project->id }}" width="300" height="300"></canvas>
        </div>
        @endforeach
    </div>
    @endrole()

    @role('admin')
    <!-- Data Table Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h2 class="mb-4">Clients and Projects</h2>
            <div class="row mb-3">
                <div class="col-md-6">
                    <select id="clientFilter" class="form-control selectpicker" data-live-search="true">
                        <option value="">All Clients</option>
                        @foreach($clients->get() as $client)
                        <option value="{{ $client->name }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="projectFilter" class="form-control selectpicker" data-live-search="true">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->name }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive full-screen-table">
                <table class="table table-bordered table-striped" id="projectsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Client</th>
                            <th>Number of Projects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endrole()

    <!-- Client Projects Modal -->
    <div class="modal fade" id="clientProjectsModal" tabindex="-1" role="dialog" aria-labelledby="clientProjectsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientProjectsModalLabel">Client Projects</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="clientProjectsList" class="list-group">
                        <!-- Projects will be dynamically populated here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @role('client|auditor')
        // Render charts for each project
        const projects = @json($projects);
        projects.forEach(project => {
            const ctx = document.getElementById('projectCompletionChart-' + project.id).getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Completed', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [
                            project.completed_statements_count,
                            project.pending_statements_count,
                            project.rejected_statements_count
                        ],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });

        @endrole()
        // Initialize DataTable with Ajax and dynamic filters
        const table = $('#projectsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard') }}",
                data: function(d) {
                    d.clientFilter = $('#clientFilter').val();
                    d.projectFilter = $('#projectFilter').val();
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'projects_count',
                    name: 'projects_count'
                }
            ],
            createdRow: function(row, data) {
                $(row).attr('data-client', data.name);
                $(row).addClass('clickable-row');
            }
        });

        // Filter functionality for client dropdown
        $('#clientFilter').on('change', function() {
            table.ajax.reload();
        });

        // Filter functionality for project dropdown
        $('#projectFilter').on('change', function() {
            table.ajax.reload();
        });

        // Filter functionality
        $('#clientFilter').on('change', function() {
            const value = $(this).val();
            table.column(0).search(value || '').draw();
        });

        $('#projectFilter').on('change', function() {
            const value = $(this).val();
            table.column(1).search(value || '').draw();
        });

        // Row click event to show client projects
        $('#projectsTable tbody').on('click', 'tr', function() {
            const clientName = $(this).data('client');
            $.ajax({
                url: "{{ route('clientProjects') }}",
                data: {
                    client: clientName
                },
                success: function(response) {
                    if (Array.isArray(response)) {
                        const list = $('#clientProjectsList');
                        list.empty();
                        response.forEach(project => {
                            list.append(`<li class="list-group-item"><a href="/projects/${project.id}">${project.name}</a></li>`);
                        });
                        $('#clientProjectsModal').modal('show');
                    } else {
                        console.error('Response is not an array:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        });
    });
</script>
@endpush
@endsection

<style>
    .container-fluid {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .project-chart-container {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        text-align: center;
    }

    .full-screen-table {
        max-height: 60vh;
        overflow-y: auto;
    }

    .full-screen-table table {
        width: 100%;
        table-layout: auto;
    }

    .clickable-row {
        cursor: pointer;
    }
</style>