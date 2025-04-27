@extends('layouts.app')

@section('content')



<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <h1 class="h4 text-white mx-auto mb-0">Spandet Dashboard</h1>
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
                <a href="#" class="list-group-item list-group-item-action">Manajemen Akun</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Kelompok</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Data</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ps-md-4">


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
    <h4>Tabel Data</h4>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary ms-2">Cari</button>
        </form>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
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
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', async () => {
            const id = row.querySelector('td:first-child').dataset.id;
            try {
                const response = await fetch(`/api/data/${id}`);
                const data = await response.json();
                
                // Update detail panel
                document.getElementById('detailContainer').style.display = 'flex';
                document.querySelectorAll('[id^="detail-"]').forEach(el => {
                    const field = el.id.replace('detail-', '');
                    el.textContent = data[field] || '-';
                });
                
                // Update image
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
            }
        });
    });
</script>
@endsection
@endsection