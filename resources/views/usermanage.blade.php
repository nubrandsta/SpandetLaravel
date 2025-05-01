@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

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
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        @if(session('password'))
        <br><strong>Password baru: {{ session('password') }}</strong>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light sidebar">
            <div class="list-group mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('group.management') }}" class="list-group-item list-group-item-action">Manajemen Grup</a>
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
                                                @foreach(['Username' => 'username', 'Full Name' => 'full_name', 'Group' => 'group', 'Creation' => 'created_at'] as $label => $column)
                                                    <th>{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr class="user-row" data-user-id="{{ $user->id }}">
                                                    <td>{{ $user->username }}</td>
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
                                <button class="btn btn-primary mb-3" id="createUserBtn">Tambah Pengguna</button>
                                <button class="btn btn-info mb-3" id="editNameBtn">Ubah Nama Lengkap</button>
                                <button class="btn btn-info mb-3" id="editGroupBtn">Ubah Grup</button>
                                <button class="btn btn-warning mb-3" id="resetPasswordBtn">Reset Kata Sandi</button>
                                <button class="btn btn-danger mb-3" id="deleteUserBtn">Hapus Pengguna</button>
                            </div>
                        </div>
                    </div>
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
                <h5 class="modal-title" id="createUserModalLabel">Buat Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <input type="text" class="form-control" id="newFullName" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="newGroup">Kelompok</label>
                        <select class="form-control" id="newGroup" name="group" required>
                            @foreach($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editNameModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editNameForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Ubah Nama Lengkap</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="editNameUserId">
          <div class="form-group">
            <label>Nama Baru</label>
            <input type="text" class="form-control" name="new_name" required>
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

<div class="modal fade" id="editGroupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editGroupForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Ubah Grup</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="editGroupUserId">
          <div class="form-group">
            <label>Grup Baru</label>
            <select class="form-control" name="new_group" id="groupSelect" required></select>
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

<div class="modal fade" id="resetPasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin reset password untuk <strong id="resetUserName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmReset">Reset</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Pengguna Berhasil Dibuat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="generatedUsername" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('generatedUsername')">Copy</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Temporary Password:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="generatedPassword" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('generatedPassword')">Copy</button>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Pengguna harus mengganti kata sandi ini pada login pertama
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="successModalOkBtn" data-bs-dismiss="modal">OK</button>
            </div>
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
    const createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    // Modal button triggers
    document.getElementById('createUserBtn').addEventListener('click', () => createUserModal.show());

    // Handle row click for user details
    let selectedUserId = null;
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            selectedUserId = row.dataset.userId;
            
            // Get user details directly from the row data instead of making an API call
            const username = row.cells[0].textContent;
            const fullName = row.cells[1].textContent;
            const group = row.cells[2].textContent;
            
            document.getElementById('userDetails').style.display = 'flex';
            document.getElementById('detailId').textContent = selectedUserId;
            document.getElementById('detailUsername').textContent = username;
            document.getElementById('detailName').textContent = fullName;
            document.getElementById('detailGroup').textContent = group;
        });
    });

    // Username validation function
    const validateUsername = (username) => /^[a-zA-Z0-9]+$/.test(username);

    // Password generator
    const generatePassword = () => {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        return Array.from({length: 8}, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    };

    // Enhanced form handling
    document.getElementById('createUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        const errorContainer = document.getElementById('createUserErrors');
        if (errorContainer) errorContainer.innerHTML = '';
        
        // Validate username format
        const username = this.username.value.trim();
        if (!validateUsername(username)) {
            showFormError('Username hanya boleh mengandung huruf dan angka');
            return;
        }
        
        // Validate full name
        const fullName = this.full_name.value.trim();
        if (fullName.length < 3) {
            showFormError('Nama lengkap minimal 3 karakter');
            return;
        }

        const formData = new FormData(this);
        try {
            console.log('Submitting form to:', this.action);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (response.ok) {
                // Success case
                createUserModal.hide();
                this.reset();
                
                // Show success modal with generated credentials
                console.log('Success data:', data); // Debug log
                
                // Only show success modal with credentials if we have a successful response
                if (data.success === true && data.username && data.password) {
                    const usernameEl = document.getElementById('generatedUsername');
                    const passwordEl = document.getElementById('generatedPassword');
                    
                    usernameEl.value = data.username;
                    passwordEl.value = data.password;
                    
                    // Make password more visible
                    passwordEl.style.fontWeight = 'bold';
                    passwordEl.style.color = '#dc3545'; // Bootstrap danger color
                    
                    successModal.show();
                } else {
                    // Something went wrong even though response was OK
                    alert('User created but credentials could not be displayed. Please check with administrator.');
                    window.location.reload();
                }
                
                // Reload page after viewing success message
                const okBtn = document.getElementById('successModalOkBtn');
                // Remove any existing event listeners
                const newOkBtn = okBtn.cloneNode(true);
                okBtn.parentNode.replaceChild(newOkBtn, okBtn);
                
                // Add new event listener
                newOkBtn.addEventListener('click', () => {
                    window.location.reload();
                });
            } else {
                // Handle validation errors
                if (data.errors) {
                    // Display specific validation errors
                    const errorMessages = Object.values(data.errors).flat();
                    showFormError(errorMessages.join('<br>'));
                } else if (data.message && data.message.includes('Duplicate entry')) {
                    // Handle duplicate entry errors
                    if (data.message.includes('username')) {
                        showFormError('Username sudah digunakan. Silakan pilih username lain.');
                    } else {
                        showFormError('Data sudah ada dalam sistem.');
                    }
                } else {
                    // Generic error
                    showFormError(data.message || 'Gagal membuat pengguna baru');
                }
            }
        } catch (error) {
            showFormError(`Error: ${error.message}`);
        }
    });
    
    // Helper function to display form errors
    function showFormError(message) {
        let errorContainer = document.getElementById('createUserErrors');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'createUserErrors';
            errorContainer.className = 'alert alert-danger mt-3';
            document.querySelector('#createUserForm .modal-body').appendChild(errorContainer);
        }
        errorContainer.innerHTML = message;
    }

    // Modal instances
    const editNameModal = new bootstrap.Modal(document.getElementById('editNameModal'));
    const editGroupModal = new bootstrap.Modal(document.getElementById('editGroupModal'));
    const resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));

    // Edit name handler
    document.getElementById('editNameBtn').addEventListener('click', () => {
        if (!selectedUserId) return alert('Pilih pengguna terlebih dahulu');
        editNameModal.show();
        document.getElementById('editNameUserId').value = selectedUserId;
    });

    // Edit group handler
    document.getElementById('editGroupBtn').addEventListener('click', async () => {
        if (!selectedUserId) return alert('Pilih pengguna terlebih dahulu');
        
        // Get current group from the user details panel
        const currentGroup = document.getElementById('detailGroup').textContent;
        
        // Populate the group select dropdown with available groups
        const groupSelect = document.getElementById('groupSelect');
        groupSelect.innerHTML = '';
        
        try {
            // Fetch groups from the API
            const response = await fetch('/api/groups');
            
            if (!response.ok) {
                throw new Error('Failed to fetch groups');
            }
            
            const allGroups = await response.json();
            
            // Add all groups except the current one
            let optionsAdded = 0;
            
            if (Array.isArray(allGroups)) {
                allGroups.forEach(group => {
                    // Skip the current group as we don't want to change to the same group
                    if (group !== currentGroup) {
                        const option = document.createElement('option');
                        option.value = group;
                        option.textContent = group;
                        groupSelect.appendChild(option);
                        optionsAdded++;
                    }
                });
            } else {
                console.error('Groups data is not an array');
                alert('Error: Data grup tidak valid. Silakan hubungi administrator.');
                return;
            }
            
            // If no options were added, show a message
            if (optionsAdded === 0) {
                alert('Tidak ada grup lain yang tersedia untuk pengguna ini.');
                return;
            }
            
            document.getElementById('editGroupUserId').value = selectedUserId;
            editGroupModal.show();
        } catch (error) {
            console.error('Error fetching groups:', error);
            alert('Gagal memuat daftar grup. Silakan coba lagi.');
        }
    });

    // Reset password handler
    document.getElementById('resetPasswordBtn').addEventListener('click', () => {
        if (!selectedUserId) return alert('Pilih pengguna terlebih dahulu');
        
        // Get username directly from the user details panel
        const username = document.getElementById('detailUsername').textContent;
        document.getElementById('resetUserName').textContent = username;
        resetPasswordModal.show();
    });

    // Reset password confirmation
    document.getElementById('confirmReset').addEventListener('click', () => {
        // Create a standard form for submission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/user/${selectedUserId}/reset-password`;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);
        
        // Add the form to the document and submit it
        document.body.appendChild(form);
        
        console.log('Resetting password for user:', selectedUserId);
        form.submit();
        // The page will reload after form submission
    });

    // Delete user handler
    document.getElementById('deleteUserBtn').addEventListener('click', () => {
        if (!selectedUserId) return alert('Pilih pengguna terlebih dahulu');
        if (confirm('Apakah anda yakin ingin menghapus pengguna ini?')) {
            console.log('Deleting user:', selectedUserId);
            
            // Create a standard form for submission
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/user/${selectedUserId}`;
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

    // Form submission handlers
    document.getElementById('editNameForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const userId = formData.get('user_id');
        const newName = formData.get('new_name');
        
        // Create a standard form for submission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/user/${userId}/name`;
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
        
        // Add the new name field
        const nameField = document.createElement('input');
        nameField.type = 'hidden';
        nameField.name = 'new_name';
        nameField.value = newName;
        form.appendChild(nameField);
        
        // Add the form to the document and submit it
        document.body.appendChild(form);
        
        console.log('Updating name for user:', userId);
        form.submit();
        // The page will reload after form submission
    });

    document.getElementById('editGroupForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const userId = formData.get('user_id');
        const newGroup = formData.get('new_group');
        
        // Create a standard form for submission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/user/${userId}/group`;
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
        
        // Add the new group field
        const groupField = document.createElement('input');
        groupField.type = 'hidden';
        groupField.name = 'new_group';
        groupField.value = newGroup;
        form.appendChild(groupField);
        
        // Add the form to the document and submit it
        document.body.appendChild(form);
        
        console.log('Updating group for user:', userId);
        form.submit();
        // The page will reload after form submission
    });

    // Form submission handling
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            createUserModal.hide();
            
            // Only set credentials if we have a successful response with username and password
            if (data.success === true && data.username && data.password) {
                console.log('Setting credentials with:', data);
                console.log('Username:', data.username);
                console.log('Password:', data.password);
                
                document.getElementById('generatedUsername').value = data.username;
                document.getElementById('generatedPassword').value = data.password;
                
                // Make password more visible
                const passwordEl = document.getElementById('generatedPassword');
                passwordEl.style.fontWeight = 'bold';
                passwordEl.style.color = '#dc3545'; // Bootstrap danger color
                
                successModal.show();
            } else {
                console.error('Invalid response data:', data);
                alert('Error: Could not retrieve user credentials');
                return;
            }
            this.reset();
        })
        .catch(error => {
            alert('Error creating user: ' + error.message);
        });
    });
    
    // Copy to clipboard function
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');
        
        // Show feedback
        const originalText = element.nextElementSibling.textContent;
        element.nextElementSibling.textContent = 'Copied!';
        setTimeout(() => {
            element.nextElementSibling.textContent = originalText;
        }, 1500);
    }
});
</script>
@endsection
@endsection
