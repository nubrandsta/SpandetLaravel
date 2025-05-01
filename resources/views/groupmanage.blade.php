@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    tr.group-row {
        cursor: pointer;
    }
    tr.group-row:hover {
        background-color: #f8f9fa;
    }
    #groupDetails .card-body {
        padding: 1.5rem;
    }

    #groupDetails p {
        margin-bottom: 0.75rem;
    }

    #groupDetails .card-title {
        margin-bottom: 1.25rem;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <h1 class="h4 text-white mx-auto mb-0">Manajemen Grup</h1>
        <a class="navbar-brand" href="#">{{ Auth::user()->full_name }}</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-light">Keluar</button>
        </form>
    </div>
</nav>

<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light sidebar">
            <div class="list-group mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('user.management') }}" class="list-group-item list-group-item-action">Manajemen Akun</a>
                <a href="{{ route('group.management') }}" class="list-group-item list-group-item-action active">Manajemen Grup</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Data</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ps-md-4">
            <div class="container">
                <h1>Manajemen Grup</h1>
    
                <div id="groupDetails" class="row mb-4" style="display: none;">
                    <div class="card" style="width: 100%;">
                        <div class="card-body">
                            <h5 class="card-title">Detail Grup</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p><strong>Nama Grup:</strong> <span id="detailGroupName"></span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p><strong>Deskripsi:</strong> <span id="detailDescription"></span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p><strong>Dibuat pada:</strong> <span id="detailCreatedAt"></span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p><strong>Diperbarui pada:</strong> <span id="detailUpdatedAt"></span></p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button id="editDescriptionBtn" class="btn btn-primary btn-sm">Edit Deskripsi</button>
                                <button id="deleteGroupBtn" class="btn btn-danger btn-sm">Hapus Grup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Daftar Grup</h5>
                                <div class="d-flex">
                                    <form class="form-inline me-2" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary ms-2">Cari</button>
                                        </div>
                                    </form>
                                    <a href="{{ route('group.management') }}" class="btn btn-outline-secondary me-2">
                                        <i class="bi-arrow-clockwise"></i> Refresh
                                    </a>
                                    <button id="createGroupBtn" class="btn btn-success">Tambah Grup</button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="d-flex align-items-center">
                                                        <span>Nama Grup</span>
                                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'group_name', 'direction' => (request('sort') === 'group_name' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none ms-2">
                                                            <i class="bi bi-arrow-down-up"></i>
                                                        </a>
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="d-flex align-items-center">
                                                        <span>Deskripsi</span>
                                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'group_description', 'direction' => (request('sort') === 'group_description' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none ms-2">
                                                            <i class="bi bi-arrow-down-up"></i>
                                                        </a>
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="d-flex align-items-center">
                                                        <span>Dibuat Pada</span>
                                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => (request('sort') === 'created_at' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none ms-2">
                                                            <i class="bi bi-arrow-down-up"></i>
                                                        </a>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groups as $group)
                                                <tr class="group-row" data-group-name="{{ $group->group_name }}">
                                                    <td>{{ $group->group_name }}</td>
                                                    <td>{{ $group->group_description }}</td>
                                                    <td>{{ $group->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $groups->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupModalLabel">Buat Grup Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createGroupForm" method="POST" action="{{ route('group.store') }}">
                @csrf
                <div class="modal-body">
                    <div id="createGroupErrors" class="alert alert-danger" style="display: none;"></div>
                    <div class="mb-3">
                        <label for="newGroupName" class="form-label">Nama Grup</label>
                        <input type="text" class="form-control" id="newGroupName" name="group_name" required>
                        <small class="text-muted">Nama grup harus unik dan tidak boleh 'admin'</small>
                    </div>
                    <div class="mb-3">
                        <label for="newGroupDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="newGroupDescription" name="group_description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Description Modal -->
<div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDescriptionModalLabel">Edit Deskripsi Grup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDescriptionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editGroupName" name="group_name">
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix for aria-hidden warning
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            this.setAttribute('aria-hidden', 'false');
        });
        modal.addEventListener('hidden.bs.modal', function() {
            this.setAttribute('aria-hidden', 'true');
        });
    });

    // Initialize Bootstrap 5 modals
    const createGroupModal = new bootstrap.Modal(document.getElementById('createGroupModal'));
    const editDescriptionModal = new bootstrap.Modal(document.getElementById('editDescriptionModal'));

    // Modal button triggers
    document.getElementById('createGroupBtn').addEventListener('click', () => createGroupModal.show());

    // Handle row click for group details
    let selectedGroupName = null;
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', async () => {
            selectedGroupName = row.dataset.groupName;
            
            try {
                const response = await fetch(`/api/groups/${selectedGroupName}`);
                if (!response.ok) {
                    throw new Error(`Error: ${response.statusText}`);
                }
                
                const group = await response.json();
                
                document.getElementById('groupDetails').style.display = 'block';
                document.getElementById('detailGroupName').textContent = group.group_name || '-';
                document.getElementById('detailDescription').textContent = group.group_description || '-';
                document.getElementById('detailCreatedAt').textContent = group.created_at || '-';
                document.getElementById('detailUpdatedAt').textContent = group.updated_at || '-';
                
                // Disable delete button for admin group
                const deleteBtn = document.getElementById('deleteGroupBtn');
                if (group.group_name === 'admin') {
                    deleteBtn.disabled = true;
                    deleteBtn.title = 'Grup admin tidak dapat dihapus';
                } else {
                    deleteBtn.disabled = false;
                    deleteBtn.title = '';
                }
            } catch (error) {
                console.error('Error fetching group details:', error);
                alert(`Error: ${error.message}`);
            }
        });
    });

    // Edit description handler
    document.getElementById('editDescriptionBtn').addEventListener('click', () => {
        if (!selectedGroupName) return alert('Pilih grup terlebih dahulu');
        
        // Get current description from the group details panel
        const currentDescription = document.getElementById('detailDescription').textContent;
        
        document.getElementById('editGroupName').value = selectedGroupName;
        document.getElementById('editDescription').value = currentDescription;
        
        editDescriptionModal.show();
    });

    // Edit description form submission
    document.getElementById('editDescriptionForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const groupName = document.getElementById('editGroupName').value;
        const description = formData.get('description');
        
        // Create a standard form for submission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/group/${groupName}/description`;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);
        
        // Add method spoofing
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        form.appendChild(methodField);
        
        // Add the description field
        const descriptionField = document.createElement('input');
        descriptionField.type = 'hidden';
        descriptionField.name = 'description';
        descriptionField.value = description;
        form.appendChild(descriptionField);
        
        // Add the form to the document and submit it
        document.body.appendChild(form);
        form.submit();
        // The page will reload after form submission
    });

    // Delete group handler
    document.getElementById('deleteGroupBtn').addEventListener('click', () => {
        if (!selectedGroupName) return alert('Pilih grup terlebih dahulu');
        if (selectedGroupName === 'admin') return alert('Grup admin tidak dapat dihapus');
        
        if (confirm(`Apakah anda yakin ingin menghapus grup ${selectedGroupName}?`)) {
            // Create a standard form for submission
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/group/${selectedGroupName}`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfField);
            
            // Add method spoofing
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Add the form to the document and submit it
            document.body.appendChild(form);
            form.submit();
            // The page will reload after form submission
        }
    });

    // Create group form submission
    document.getElementById('createGroupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        const errorContainer = document.getElementById('createGroupErrors');
        errorContainer.style.display = 'none';
        errorContainer.innerHTML = '';
        
        // Create a standard form for submission instead of using fetch
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.action;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);
        
        // Add form fields
        const groupNameField = document.createElement('input');
        groupNameField.type = 'hidden';
        groupNameField.name = 'group_name';
        groupNameField.value = document.getElementById('newGroupName').value;
        form.appendChild(groupNameField);
        
        const groupDescField = document.createElement('input');
        groupDescField.type = 'hidden';
        groupDescField.name = 'group_description';
        groupDescField.value = document.getElementById('newGroupDescription').value;
        form.appendChild(groupDescField);
        
        // Add the form to the document and submit it
        document.body.appendChild(form);
        form.submit();
        // The page will reload after form submission
    });
});
</script>
@endsection
@endsection