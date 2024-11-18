<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        #header {
            background-color: #43644C;
            color: white;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1020;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #header .navbar {
            padding: 0;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #43644C;
            color: white;
            height: 100%;
            position: fixed;
            top: 56px;
            left: 0;
            z-index: 1010;
            overflow-y: auto;
        }

        #sidebar a {
            color: white;
            text-decoration: none;
        }

        #sidebar a:hover {
            color: #adb5bd;
        }

        #content {
            margin-left: 250px;
            margin-top: 56px;
            padding: 20px;
            flex-grow: 1;
        }

        @media (max-width: 768px) {
            #sidebar {
                position: absolute;
                left: -250px;
                transition: left 0.3s ease;
            }

            #sidebar.active {
                left: 0;
            }

            #content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<!-- Header Navbar -->
<div id="header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">{{ config('app.name', 'Laravel') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        User
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
{{--                        <a class="dropdown-item" href="#">Profile</a>--}}
{{--                        <a class="dropdown-item" href="#">Settings</a>--}}
{{--                        <div class="dropdown-divider"></div>--}}
                        <a class="dropdown-item" href="#">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>

<!-- Sidebar -->
<div id="sidebar">
    <div class="text-center py-3">
        <!-- Replace with your project logo -->
        <img src="{{ asset('Origin Logo-01.png') }}" alt="Project Logo" class="img-fluid" style="max-width: 150px;">
    </div>
    <ul class="nav flex-column px-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('permissions.index') }}">Permissions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('projects.index') }}">Projects</a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div id="content">
    <!-- Sidebar toggle button for smaller screens -->
    <button class="btn btn-dark d-md-none mb-3" id="sidebarToggle">☰ Toggle Sidebar</button>

    @yield('content')
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
</script>

@stack('scripts')
</body>
</html>
