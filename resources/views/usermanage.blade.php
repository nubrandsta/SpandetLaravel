@extends('layouts.app')

@section('content')

<style>
    tr.user-row {
        cursor: pointer;
    }
    tr.user-row:hover {
        background-color: #f8f9fa;
    }
    #userDetails .card-body {
        padding: 1.5rem;

    }

    #userDetails p {
        margin-bottom: 0.75rem;
    }

    #userDetails .card-title {
        margin-bottom: 1.25rem;
    }

    #userDetails .row {
        margin-left: -1.5rem;
        margin-right: -1.5rem;
    }

    #userDetails .col-md-6 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }

    #d-flex flex-column {
        margin-left: 40em;
        margin-right: 40em;
        margin-top: 2em;
        margin-bottom: 2em;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <h1 class="h4 text-white mx-auto mb-0">Manajemen Akun</h1>
        <a class="navbar-brand" href="#">{{ Auth::user()->full_name }}</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-light">Keluar</button>
        </form>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light sidebar">
            <div class="list-group mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Kelompok</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Data</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ps-md-4">
            <div class="container">
                <h1>User Management</h1>
    
                <div id="userDetails" class="row mb-4" style="display: none;">
                    <div class="card" style="width: 100%;">
                        <div class="card-body">
                            <h5 class="card-title">Detail Pengguna</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p><strong>User ID:</strong> <span id="detailId"></span></p>
                                    <p><strong>Username:</strong> <span id="detailUsername"></span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p><strong>Nama Lengkap:</strong> <span id="detailName"></span></p>
                                    <p><strong>Grup:</strong> <span id="detailGroup"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Akun</h5>
                    <form class="form-inline" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary ms-2">Cari
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('user.management') }}" class="btn btn-outline-secondary">
                                    <i class="bi-arrow-clockwise"></i> Refresh
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    @php
                                        $sortColumns = ['username', 'full_name', 'group', 'created_at'];
                                        $sort = request('sort');
                                        $direction = request('direction');
                                        
                                        $sortIcons = [
                                            'default' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/></svg>',
                                            'asc' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/></svg>',
                                            'desc' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/></svg>'
                                        ];
                                        
                                        $currentSort = request('sort');
                                        $currentDirection = request('direction');
                                        
                                        function getNextDirection($column, $currentSort, $currentDirection) {
                                            if ($column !== $currentSort) return 'desc';
                                            return match($currentDirection) {
                                                'desc' => 'asc',
                                                'asc' => 'desc',
                                                default => 'desc'
                                            };
                                        }
                                    @endphp
                                    @foreach(['Username' => 'username', 'Full Name' => 'full_name', 'Group' => 'group', 'Creation' => 'created_at'] as $label => $column)
                                        <th>
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span>{{ $label }}</span>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => getNextDirection($column, $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                    {!! $sortIcons[$currentSort === $column ? ($currentDirection ?: 'default') : 'default'] !!}
                                                </a>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr class="user-row" data-user-id="{{ $user->id }}">
                                        <td data-user-id="{{ $user->id }}">{{ $user->username }}</td>
                                        <td>{{ $user->full_name }}</td>
                                        <td>{{ $user->group }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Manajemen Akun</h5>
                </div>
                <div class="d-flex flex-column">
                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createUserModal">
                        <i class="fas fa-plus-circle"></i> Tambah Pengguna
                    </button>
                    <button class="btn btn-info mb-3" data-toggle="modal" data-target="#editNameModal">
                        Ubah Nama Lengkap
                    </button>
                    <button class="btn btn-info mb-3" data-toggle="modal" data-target="#editGroupModal">
                        Ubah Grup
                    </button>
                    <button class="btn btn-warning mb-3" data-toggle="modal" data-target="#resetPasswordModal">
                        Reset Kata Sandi
                    </button>
                    <button class="btn btn-danger mb-3" data-toggle="modal" data-target="#deleteUserModal">
                        Hapus Pengguna
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createUserForm" method="POST" action="{{ route('user.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newUsername">Username</label>
                        <input type="text" class="form-control" id="newUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="newFullName">Nama</label>
                        <input type="text" class="form-control" id="newFullName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="newGroup">Kelompok</label>
                        <select class="form-control" id="newGroup" name="group" required>
                            <option value="user">User</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('user-management.modals')

<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">User Created Successfully</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Username: <strong id="generatedUsername"></strong></p>
                <p>Temporary Password: <strong id="generatedPassword"></strong></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    User must change this password on first login
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#createUserForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                $('#createUserModal').modal('hide');
                $('#successModal').modal('show');
                $('#generatedUsername').text(response.username);
                $('#generatedPassword').text(response.password);
                $('#createUserForm')[0].reset();
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'Error creating user');
            }
        });
    });
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', async () => {
            const userId = row.dataset.userId;
            // Existing user detail fetch logic preserved
            try {
                const response = await fetch(`/api/users/${userId}`);
                const user = await response.json();
                
                document.getElementById('userDetails').style.display = 'flex';
                document.getElementById('detailId').textContent = user.id;
                document.getElementById('detailUsername').textContent = user.username;
                document.getElementById('detailName').textContent = user.full_name;
                document.getElementById('detailGroup').textContent = user.group;
            } catch (error) {
                console.error('Error fetching user details:', error);
            }
        });
    });

    $('#resetPasswordModal').on('show.bs.modal', function(e) {
        const userId = $('#detailId').text();
        if (!userId) {
            e.preventDefault();
            alert('Please select a user first');
        }
    });
});
</script>
@endsection
@endsection
