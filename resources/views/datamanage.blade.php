@extends('layouts.app')

@section('content')

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <h1 class="h4 text-white mx-auto mb-0">Manajemen Data</h1>
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
                <a href="{{ route('group.management') }}" class="list-group-item list-group-item-action">Manajemen Grup</a>
                <a href="{{ route('data.management') }}" class="list-group-item list-group-item-action active">Manajemen Data</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ps-md-4">
            <div class="container">
                <h1>Manajemen Data</h1>

                <div id="detailContainer" class="row mb-4" style="display: none;">
                    <div class="col-md-8">
                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="row small g-2">
                                <div class="col-6"><span class="text-muted">Uploader:</span> <span id="detail-uploader">-</span></div>
                                <div class="col-6"><span class="text-muted">Waktu:</span> <span id="detail-createdAt">-</span></div>
                                <div class="col-6"><span class="text-muted">Lat:</span> <span id="detail-lat">-</span></div>
                                <div class="col-6"><span class="text-muted">Long:</span> <span id="detail-long">-</span></div>
                                <div class="col-6"><span class="text-muted">Area 1:</span> <span id="detail-thoroughfare">-</span></div>
                                <div class="col-6"><span class="text-muted">Area 2:</span> <span id="detail-subLocality">-</span></div>
                                <div class="col-6"><span class="text-muted">Area 3:</span> <span id="detail-locality">-</span></div>
                                <div class="col-6"><span class="text-muted">Area 4:</span> <span id="detail-subAdmin">-</span></div>
                                <div class="col-6"><span class="text-muted">Area 5:</span> <span id="detail-adminArea">-</span></div>
                                <div class="col-6"><span class="text-muted">Kode Pos:</span> <span id="detail-postalCode">-</span></div>
                            </div>
                            <div class="mt-3">
                                <button id="deleteDataBtn" class="btn btn-danger btn-sm">Hapus Data</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded shadow-sm text-center h-100 d-flex align-items-center justify-content-center">
                            <img id="detail-image" src="" class="img-fluid" style="display: none;" onerror="this.style.display='none';document.getElementById('image-placeholder').style.display='block'" />
                            <div id="image-placeholder" class="text-muted w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                </svg>
                                <div class="small mt-2">No image available</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="d-flex gap-2">
                        <a href="{{ route('data.export', request()->query()) }}" class="btn btn-success">Export CSV</a>
                        <a href="{{ route('data.export.excel', request()->query()) }}" class="btn btn-info">Export Excel</a>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="btn btn-primary ms-2">Cari</button>
                        </form>
                        <a href="{{ route('data.management') }}" class="btn btn-outline-secondary">
                            <i class="bi-arrow-clockwise"></i> Refresh
                        </a>
                    </div>
                </div>

                <div id="dataTableContainer">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    @php
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
                                                'asc' => null,
                                                default => 'desc'
                                            };
                                        }
                                    @endphp
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Waktu</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => getNextDirection('created_at', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'created_at' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Uploader</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'uploader', 'direction' => getNextDirection('uploader', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'uploader' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Kelompok</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'group', 'direction' => getNextDirection('group', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'group' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Jml Spanduk</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'spandukCount', 'direction' => getNextDirection('spandukCount', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'spandukCount' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Area 1</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'thoroughfare', 'direction' => getNextDirection('thoroughfare', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'thoroughfare' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Area 2</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'subLocality', 'direction' => getNextDirection('sublocality', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'subLocality' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Area 3</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'locality', 'direction' => getNextDirection('locality', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'locality' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Area 4</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'subAdmin', 'direction' => getNextDirection('subadmin', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'subAdmin' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Area 5</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'adminArea', 'direction' => getNextDirection('adminArea', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'adminArea' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <span>Kode Pos</span>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'postalCode', 'direction' => getNextDirection('postalcode', $currentSort, $currentDirection)]) }}" class="text-decoration-none ms-2" style="cursor: pointer">
                                                {!! $sortIcons[$currentSort === 'postalCode' ? ($currentDirection ?: 'default') : 'default'] !!}
                                            </a>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td data-id="{{ $item->id }}">{{ $item->created_at->format('d M Y H:i:s') }}</td>
                                    <td>{{ $item->uploader }}</td>
                                    <td>{{ $item->group }}</td>
                                    <td>{{ $item->spandukCount }}</td>
                                    <td>{{ $item->thoroughfare }}</td>
                                    <td>{{ $item->subLocality }}</td>
                                    <td>{{ $item->locality }}</td>
                                    <td>{{ $item->subAdmin }}</td>
                                    <td>{{ $item->adminArea }}</td>
                                    <td>{{ $item->postalCode }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDataModal" tabindex="-1" aria-labelledby="deleteDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDataModalLabel">Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah anda yakin ingin menghapus data ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
    #detailContainer [id^="detail-"] {
        font-size: 0.875rem;
        line-height: 1.3;
        word-break: break-word;
    }
    #detailContainer .row {
        align-items: stretch;
    }
    #detail-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        flex-shrink: 1;
    }
    #image-placeholder {
        min-height: 200px;
    }
    
    #detailContainer .col-md-4 > div {
        width: 300px;
        height: 300px;
        min-width: 300px;
        min-height: 300px;
    }
    tr {
        cursor: pointer;
    }
    tr:hover {
        background-color: #f8f9fa;
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap 5 modals
        const deleteDataModal = new bootstrap.Modal(document.getElementById('deleteDataModal'));
        
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
        
        let selectedDataId = null;
        
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', async () => {
                const dataId = row.querySelector('td[data-id]').dataset.id;
                if (!dataId) return;
                
                selectedDataId = dataId;
                
                // Show the detail container
                const detailContainer = document.getElementById('detailContainer');
                detailContainer.style.display = 'flex';
                
                try {
                    // Fetch data details
                    const response = await fetch(`/api/data/${dataId}`);
                    if (!response.ok) {
                        throw new Error(`Error: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    
                    // Update detail fields
                    document.getElementById('detail-uploader').textContent = data.uploader || '-';
                    document.getElementById('detail-lat').textContent = data.lat || '-';
                    document.getElementById('detail-long').textContent = data.long || '-';
                    document.getElementById('detail-thoroughfare').textContent = data.thoroughfare || '-';
                    document.getElementById('detail-subLocality').textContent = data.subLocality || '-';
                    document.getElementById('detail-locality').textContent = data.locality || '-';
                    document.getElementById('detail-subAdmin').textContent = data.subAdmin || '-';
                    document.getElementById('detail-adminArea').textContent = data.adminArea || '-';
                    document.getElementById('detail-postalCode').textContent = data.postalCode || '-';
                    document.getElementById('detail-createdAt').textContent = data.createdAt || '-';
                    
                    // Handle image
                    const img = document.getElementById('detail-image');
                    const placeholder = document.getElementById('image-placeholder');
                    
                    if (data.image_url) {
                        img.src = data.image_url;
                        img.style.display = 'block';
                        placeholder.style.display = 'none';
                    } else {
                        img.style.display = 'none';
                        placeholder.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Error fetching data:', error);
                    alert(`Error fetching data details: ${error.message}`);
                }
            });
        });
        
        // Delete data button click handler
        document.getElementById('deleteDataBtn').addEventListener('click', () => {
            if (!selectedDataId) {
                alert('Pilih data terlebih dahulu');
                return;
            }
            
            deleteDataModal.show();
        });
        
        // Confirm delete button click handler
        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!selectedDataId) return;
            
            try {
                const response = await fetch(`/api/data/${selectedDataId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete data');
                }
                
                // Close the modal and reload the page
                deleteDataModal.hide();
                window.location.reload();
            } catch (error) {
                console.error('Error deleting data:', error);
                alert(`Error: ${error.message}`);
            }
        });
        
        // Export functionality is now handled by the direct link to the export route
    });
</script>
@endsection
@endsection