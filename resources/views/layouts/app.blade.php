<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spandet Dashboard</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            @auth
                <button class="btn btn-link text-white" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
            @endauth
            <h1 class="h4 text-white mx-auto mb-0">Spandet Dashboard</h1>
            @auth
                <a class="navbar-brand" href="{{ route('dashboard') }}">{{ Auth::user()->full_name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light">Keluar</button>
                </form>
            @endauth
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 bg-light sidebar position-fixed h-100 collapsed" id="sidebar" style="z-index: 1000; transition: all 0.3s;">
                    <div class="list-group mt-3">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('user.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('user.management') ? 'active' : '' }}">Manajemen Akun</a>
                        <a href="{{ route('group.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('group.management') ? 'active' : '' }}">Manajemen Grup</a>
                        <a href="{{ route('data.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('data.management') ? 'active' : '' }}">Manajemen Data</a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9 col-lg-10 ms-auto expanded" id="mainContent">
                    @yield('content')
                </div>
            @else
                <!-- Main Content (Full Width for Non-Authenticated Users) -->
                <div class="col-12">
                    @yield('content')
                </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Sidebar styles */
        #sidebar {
            left: 0;
            top: 0;
            padding-top: 60px;
            width: 250px;
        }

        #sidebar.collapsed {
            left: -250px;
        }

        #mainContent {
            transition: margin-left 0.3s;
            margin-left: 250px;
        }

        #mainContent.expanded {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            #sidebar {
                left: -250px;
            }
            
            #sidebar.show {
                left: 0;
            }

            #mainContent {
                margin-left: 0;
            }
        }

        /* Navbar styles */
        .navbar {
            padding: 0.5rem 1rem;
        }

        #sidebarToggle {
            padding: 0.25rem 0.5rem;
            margin-right: 0.5rem;
        }

        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
    </style>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            function toggleSidebar() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
            
            sidebarToggle.addEventListener('click', toggleSidebar);
            
            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = sidebarToggle.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggle && !sidebar.classList.contains('collapsed')) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            });
        });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
