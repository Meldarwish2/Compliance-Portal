@extends('layouts.master2')

@section('head')
@endsection

@php
    $cardClass = (Auth::user()->hasRole('admin')) ? 'col-xl-4 col-md-6' : 'col-md-6';
@endphp

@section('content')
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0">Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="row">
            <div class="{{ $cardClass }}">
                <div class="card bg-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-2"><span class="counter-value" data-target="{{ $projectsData['totalProjects'] }}">{{ $projectsData['totalProjects'] }}</span></h4>
                                <p class="text-uppercase fw-medium fs-14 text-muted mb-0">Total Projects</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-light rounded-circle fs-3">
                                <i class="bx bx-file fs-22 text-primary"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(Auth::user()->hasRole('admin'))
                <div class="{{ $cardClass }}">
                    <div class="card" style="background-color: #4b4375;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-2"><span class="counter-value" data-target="{{ $totalUsers }}">{{ $totalUsers }}</span></h4>
                                    <p class="text-uppercase fw-medium fs-14 text-muted mb-0">Total Users</p>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-light rounded-circle fs-3">
                                <i class="bx bx-edit fs-22 text-primary"></i>
                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="{{ $cardClass }}">
                <div class="card" style="background-color: #120d29;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-white"><span class="counter-value" data-target="{{ $pendingActions }}">{{ $pendingActions }}</span></h4>
                                <p class="text-uppercase fw-medium fs-14 text-white-50 mb-0">Pending Projects</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-light-subtle rounded-circle fs-3">
                                <i class="bx bx-time fs-22 text-white"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Projects Overview</h5>
                    </div>
                    <div class="card-body">
                        <div id="projects-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Only show the table for Admin -->
        @if(Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-12">
                    <!-- Table Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Projects</h5>
                        </div>
                        <div class="card-body">
                            <table id="model-datatables" class="table table-bordered nowrap  align-center" style="width:100%">
                                <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Assignee Users</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <th scope="row">{{$project->id}}</th>
                                        <td>{{$project->name}}</td>
                                        <td>{{$project->description}}</td>
                                        <td>
                                            <div class="avatar-group">
                                                @foreach ($project->users as $project_user)
                                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $project_user->name }}">
                                                        <!-- Display the first two characters of the project name -->
                                                        <span class="avatar-title bg-warning rounded-circle fs-3">
                                                {{ strtoupper(substr($project_user->name, 0, 2)) }}
                                            </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Include ApexCharts library -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                var seriesData = {!! $projects->map(function($project) {
            return [
                'x' => $project->name,
                'y' => $project->completionPercentage,
                'color' => $project->color,
            ];
        })->toJson() !!};
                var options = {
                    series: [{
                        name: '%',
                        data: seriesData.map(item => ({
                            x: item.x,
                            y: item.y
                        }))
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            columnWidth: '10%',
                            distributed: true  // Enable distributed color for each bar
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: seriesData.map(item => item.x),
                        labels: {
                            rotate: -45
                        },
                        tickPlacement: 'on',
                        axisBorder: {
                            show: true
                        },
                        axisTicks: {
                            show: true
                        },
                        show: true
                    },
                    yaxis: {
                        min: 2,  // Set the minimum value to 2%
                        max: 100,  // Set the maximum value to 100%
                        forceNiceScale: true,  // Ensure integer steps
                        title: {
                            text: 'Completion Percentage',
                        },
                        labels: {
                            formatter: function(value) {
                                return value + '%';  // Append '%' to y-axis labels
                            }
                        }
                    },
                    fill: {
                        type: 'solid',
                        colors: seriesData.map(item => item.color)
                    }
                };

                var chart = new ApexCharts(document.querySelector("#projects-chart"), options);
                chart.render();
            });
        </script>





        <!-- Add custom styles for the hover effect -->
        <style>
            .avatar-group-item {
                position: relative;
            }

            .avatar-title {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;  /* Adjust circle size */
                height: 40px; /* Adjust circle size */
                font-size: 16px; /* Adjust text size */
                transition: background-color 0.3s, color 0.3s, transform 0.3s, box-shadow 0.3s; /* Smooth transition */
            }

            .avatar-group-item:hover .avatar-title {
                background-color: #f1c40f; /* Change background color on hover */
                color: white; /* Change text color on hover */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow on hover */
                transform: scale(1.1); /* Slightly enlarge the circle */
            }
        </style>

    @push('scripts')
    @endpush

@endsection
