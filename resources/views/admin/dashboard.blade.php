@extends('layouts.master')

@section('content')
<div class="container">
   
    @foreach($projects as $project)
        <div class="project-chart-container">
            <h2>{{ $project->name }}</h2>
            <canvas id="projectCompletionChart-{{ $project->id }}"></canvas>
        </div>
    @endforeach
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const projects = @json($projects);

        projects.forEach(project => {
            const ctx = document.getElementById('projectCompletionChart-' + project.id).getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Completed', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [project.completed_statements_count, project.pending_statements_count, project.rejected_statements_count],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    }],
                },
                options: {
                    responsive: false, // Disable responsive resizing
                    maintainAspectRatio: false, // Allow the chart to fill the canvas
                }
            });
        });
    });
</script>
@endpush
@endsection

<style>
    .container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .project-chart-container {
        width: 300px;
        height: 300px;
        margin-bottom: 20px;
    }
</style>