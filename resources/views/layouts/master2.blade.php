<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Dashboard | Reseliency</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Minimal Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets2/images/favicon.ico')}}">

    <!-- jsvectormap css -->
    <link href="{{asset('assets2/libs/jsvectormap/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />


    <!--Swiper slider css-->
    <link href="{{asset('assets2/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- Sweet Alert css-->
    <link href="{{asset('assets2/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Layout config Js -->
    <script src="{{asset('assets2/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets2/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets2/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets2/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('assets2/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="index.html" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('assets2/images/logo-sm.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('assets2/images/logo-dark.png')}}" alt="" height="22">
                                </span>
                            </a>

                            <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset('assets2/images/logo-sm.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('assets2/images/logo-light.png')}}" alt="" height="22">
                                </span>
                            </a>
                        </div>


                    </div>



                </div>

            </div>
    </div>
    </header>


    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <div class="container-fluid">

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{route('dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets2/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets2/images/logo-dark.png')}}" alt="" height="22">
                    </span>
                </a>
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('assets2/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets2/images/logo-light.png')}}" alt="" height="22">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu">
                    </div>

                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                        <li class="nav-item">
                            <a href="{{route('dashboard')}}" class="nav-link menu-link"> <i class="ri-dashboard-2-line"></i> <span
                                    data-key="t-dashboard">Dashboard</span> </a>
                        </li>
                        @role('admin')
                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-apps">Permissions & Roles</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarApps">
                                <i class="ri-apps-2-line"></i> <span data-key="t-apps">Permissions & Roles</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarApps">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('permissions.index')}}" class="nav-link" data-key="t-calendar">Permissions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('roles.index')}}" class="nav-link" data-key="t-chat">Roles </a>
                                    </li>




                                </ul>
                            </div>
                        </li>
                        @endrole
                        @role('admin')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarUI">
                                <i class="ri-pencil-ruler-2-line"></i> <span data-key="t-bootstrap-ui">User Management</span>
                            </a>
                            <div class="collapse menu-dropdown " id="sidebarUI">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('users.index')}}" class="nav-link" data-key="t-alerts">Users</a>
                                            </li>

                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </li>
                        @endrole
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarUI">
                                <i class="ri-pencil-ruler-2-line"></i> <span data-key="t-bootstrap-ui">Projects Management</span>
                            </a>
                            <div class="collapse menu-dropdown " id="sidebarUI">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('projects.index')}}" class="nav-link" data-key="t-alerts">Projects</a>
                                            </li>

                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </li>
                        @role('admin')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarUI">
                                <i class="ri-pencil-ruler-2-line"></i> <span data-key="t-bootstrap-ui">Audit Management</span>
                            </a>
                            <div class="collapse menu-dropdown " id="sidebarUI">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('audits.index')}}" class="nav-link" data-key="t-alerts">Audit Trial</a>
                                            </li>

                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </li>
                        @endrole



                    </ul>


                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-profile-menu text-center d-flex">
                <div class="d-flex align-items-center">




                    <div class="dropdown header-item">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle header-profile-user" src="{{asset('assets2/images/users/user-dummy-img.jpg')}}"
                                    alt="Header Avatar">
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Welcome {{Auth::user()->name ?? ''}}</h6>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">

                                <i
                                    class="bx bx-log-out text-muted fs-17 align-middle me-1"></i> <span
                                    class="align-middle" data-key="t-logout"> {{ __('Logout') }}</span></a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif


                @yield('content')


                <!-- end row -->


            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Reseliency.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">

                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->





    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>



    <!-- JAVASCRIPT -->
    <script src="{{asset('assets2/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets2/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets2/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('assets2/js/plugins.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- apexcharts -->
    <script src="{{asset('assets2/libs/apexcharts/apexcharts.min.js')}}"></script>
    <!-- piecharts init -->
    <script src="{{asset('assets2/js/pages/apexcharts-pie.init.js')}}"></script>

    <!--Swiper slider js-->
    <!-- <script src="{{asset('assets2/libs/swiper/swiper-bundle.min.js')}}"></script> -->

    <!-- Dashboard init -->
    <!-- <script src="{{asset('assets2/js/pages/dashboard.init.js')}}"></script> -->
    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{asset('assets2/js/pages/datatables.init.js')}}"></script>



    <!-- prismjs plugin -->
    <!-- <script src="{{asset('assets2/libs/prismjs/prism.js')}}"></script> -->
    <!-- <script src="{{asset('assets2/libs/list.js/list.min.js')}}"></script> -->
    <!-- <script src="{{asset('assets2/libs/list.pagination.js/list.pagination.min.js')}}"></script> -->

    <!-- listjs init -->
    <!-- <script src="{{asset('assets2/js/pages/listjs.init.js')}}"></script> -->

    <!-- Sweet Alerts js -->
    <script src="{{asset('assets2/libs/sweetalert2/sweetalert2.min.js')}}"></script>

    <!-- Modal Js -->
    <!-- <script src="{{asset('assets2/js/pages/modal.init.js')}}"></script> -->

    <script src="{{asset('assets2/libs/rater-js/index.js')}}"></script>
    <!-- rating init -->
    <script src="{{asset('assets2/js/pages/rating.init.js')}}"></script>

    <!-- App js -->
    <script src="{{asset('assets2/js/app.js')}}"></script>

    @stack('scripts')

</body>

</html>