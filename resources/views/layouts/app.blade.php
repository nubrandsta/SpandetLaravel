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

    @auth
        <!-- Sidebar (overlay) -->
        <div class="sidebar bg-light position-fixed h-100 collapsed" id="sidebar" style="z-index: 1050; top: 0; left: -250px; width: 250px; transition: left 0.3s; padding-top: 60px;">
            <div class="d-flex align-items-center justify-content-end p-2">
                <button class="btn btn-link text-primary" id="sidebarCollapseBtn" style="font-size: 1.5rem;">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
            <div class="list-group mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('user.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('user.management') ? 'active' : '' }}">Manajemen Akun</a>
                <a href="{{ route('group.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('group.management') ? 'active' : '' }}">Manajemen Grup</a>
                <a href="{{ route('data.management') }}" class="list-group-item list-group-item-action {{ request()->routeIs('data.management') ? 'active' : '' }}">Manajemen Data</a>
            </div>
        </div>
    @endauth

    <!-- Main Content (always full width) -->
    <div id="mainContent">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Sidebar overlay styles */
        #sidebar {
            left: -250px;
            width: 250px;
            top: 0;
            height: 100vh;
            background: #f8f9fa;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }
        #sidebar:not(.collapsed) {
            left: 0;
        }
        #sidebar.collapsed {
            left: -250px;
        }
        #mainContent {
            width: 100%;
            margin-left: 0;
            transition: none;
        }
        /* Overlay effect for sidebar */
        #sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.2);
            z-index: 1049;
        }
        #sidebar.show ~ #sidebar-backdrop {
            display: block;
        }
        @media (max-width: 768px) {
            #sidebar {
                width: 80vw;
                min-width: 200px;
                max-width: 300px;
            }
        }
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
    <div id="sidebar-backdrop"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');

            function openSidebar() {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('show');
                if (sidebarBackdrop) sidebarBackdrop.style.display = 'block';
            }
            function closeSidebar() {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('show');
                if (sidebarBackdrop) sidebarBackdrop.style.display = 'none';
            }
            function toggleSidebar() {
                if (sidebar.classList.contains('collapsed')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            }
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeSidebar();
                });
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            // Optional: close sidebar on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeSidebar();
            });
        });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
