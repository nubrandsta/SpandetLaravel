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
        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="navDropdown" style="display: none; position: absolute; left: 10px; top: 60px; z-index: 1050; min-width: 200px;">
            <a href="{{ route('dashboard') }}" class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('user.management') }}" class="dropdown-item {{ request()->routeIs('user.management') ? 'active' : '' }}">Manajemen Akun</a>
            <a href="{{ route('group.management') }}" class="dropdown-item {{ request()->routeIs('group.management') ? 'active' : '' }}">Manajemen Grup</a>
            <a href="{{ route('data.management') }}" class="dropdown-item {{ request()->routeIs('data.management') ? 'active' : '' }}">Manajemen Data</a>
        </div>
    @endauth

    <!-- Main Content -->
    <div id="mainContent">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
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
            const dropdown = document.getElementById('navDropdown');
            const toggleBtn = document.getElementById('sidebarToggle');

            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });

            // Close dropdown when clicking outside
             document.addEventListener('click', function(e) {
                 if (!dropdown.contains(e.target) && e.target !== toggleBtn) {
                     dropdown.style.display = 'none';
                 }
             });

            // Close dropdown on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
        });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
