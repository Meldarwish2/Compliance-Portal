@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    <div id="chart-container">
        <canvas id="projectCompletionChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('projectCompletionChart').getContext('2d');
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.values,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }],
        },
    });
</script>
@endpush
@endsection
