@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">

<!-- Mapbox CSS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css' rel='stylesheet' />

<!-- Pass Mapbox token to JavaScript -->
<script>
    const MAPBOX_TOKEN = "{{ config('services.mapbox.token', env('MAPBOX_TOKEN')) }}";
</script>

<!-- Map Container - Full Width -->
<div class="mb-4">
    <div>
        <div id="map" class="rounded shadow-sm" style="height: 500px;">
                        <div class="d-flex justify-content-center align-items-center h-100 bg-light">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading map... If the map doesn't appear, please check your Mapbox token in the .env file.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!-- Detail Container with Image Panel Inside -->
<div class="mb-4">
    <div id="detailContainer" class="bg-white p-3 rounded shadow-sm h-100" style="display: none;"
                        <div class="row small g-2">
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-6 col-md-3"><span class="text-muted">Uploader:</span> <span id="detail-uploader">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Kelompok:</span> <span id="detail-group">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Jml Spanduk:</span> <span id="detail-spandukCount">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Waktu:</span> <span id="detail-createdAt">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Lat:</span> <span id="detail-lat">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Long:</span> <span id="detail-long">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Area 1:</span> <span id="detail-thoroughfare">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Area 2:</span> <span id="detail-subLocality">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Area 3:</span> <span id="detail-locality">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Area 4:</span> <span id="detail-subAdmin">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Area 5:</span> <span id="detail-adminArea">-</span></div>
                                    <div class="col-6 col-md-3"><span class="text-muted">Kode Pos:</span> <span id="detail-postalCode">-</span></div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-white p-3 rounded shadow-sm text-center h-100">
                                    <img id="detail-image" 
                                         src="" 
                                         class="img-fluid cursor-pointer" 
                                         style="cursor: pointer; height: 100%; width: auto; object-fit: contain;" 
                                         onclick="openImageModal(this.src)" />
                                </div>
                            </div>
                        </div>
        </div>
    </div>
    <!-- Image Panel Inside Detail Container -->
        <div style="min-width: 300px; max-width: 400px; margin-left: auto;" id="imagePanel">
            <div class="bg-white p-3 rounded shadow-sm text-center h-100">
                <img id="detail-image" 
                      src="" 
                      class="img-fluid cursor-pointer h-100" 
                      style="cursor: pointer; width: auto; object-fit: contain;" 
                     onclick="openImageModal(this.src)" />
                 <div id="image-placeholder" class="text-muted w-100 cursor-pointer" style="display: none;" onclick="openImageModal(document.getElementById('detail-image').src)">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                </svg>
                <div class="small mt-2">No image available</div>
                    </div>
                </div>
            </div>
</div>

<!-- Data Table Section -->
<div>
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
                {{ $data->links('vendor.pagination.simple-bootstrap-5') }}
                </div>
            </div>
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
    
    #detail-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        flex-shrink: 1;
    }
    
    #image-placeholder {
        min-height: 200px;
    }
    
    tr {
        cursor: pointer;
    }
    
    tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Mapbox popup styles */
    .mapboxgl-popup {
        max-width: 300px;
    }
    
    .mapboxgl-popup-content {
        padding: 15px;
    }
    
    .map-popup-content h5 {
        margin-top: 0;
        margin-bottom: 8px;
    }
    
    .map-popup-content p {
        margin-bottom: 5px;
    }

    /* Sidebar styles */
    #sidebar {
        left: 0;
        top: 0;
        padding-top: 60px;
    }

    #sidebar.collapsed {
        left: -250px;
    }

    #mainContent {
        transition: margin-left 0.3s;
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
    }
</style>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Gambar Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modal-image" src="" class="img-fluid" alt="Full Image">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Mapbox JS -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js'></script>

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
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Initialize Mapbox map
        mapboxgl.accessToken = MAPBOX_TOKEN; // Using token from the variable we defined
        
        try {
            // Check if token is valid
            if (!MAPBOX_TOKEN || MAPBOX_TOKEN === "" || MAPBOX_TOKEN.includes("your_mapbox")) {
                throw new Error('Invalid or missing Mapbox token');
            }
            
            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [104.4458, 0.9186], // Default center (Tanjung Pinang)
                zoom: 12
            });
            
            // Add error handler for map load failures
            map.on('error', function(e) {
                console.error('Mapbox error:', e);
                document.getElementById('map').innerHTML = 
                    '<div class="alert alert-warning">Error loading map. Please check your Mapbox token.</div>';
            });
        
            // Add navigation controls
            map.addControl(new mapboxgl.NavigationControl());
            
            // Add markers for all data points
            let markers = [];
            
            // Function to load data points and add markers
            async function loadDataPoints() {
                try {
                    // Get all table rows
                    const rows = document.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const dataId = row.querySelector('td[data-id]')?.dataset.id;
                        if (!dataId) return;
                        
                        // Get lat/long from the row if available
                        const cells = row.querySelectorAll('td');
                        let lat, long, uploader, location;
                        
                        // Extract data from table cells if available
                        // This depends on your table structure, adjust indices as needed
                        if (cells.length >= 5) {
                            uploader = cells[1]?.textContent || 'Unknown';
                            location = cells[4]?.textContent || 'Unknown';
                        }
                        
                        // Add click event to row that also centers the map on the marker
                        row.addEventListener('click', async () => {
                            const dataId = row.querySelector('td[data-id]').dataset.id;
                            if (!dataId) return;
                            
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
                                document.getElementById('detail-group').textContent = data.group || '-';
                                document.getElementById('detail-spandukCount').textContent = data.spandukCount || '-';
                                document.getElementById('detail-createdAt').textContent = data.createdAt || '-';
                                document.getElementById('detail-lat').textContent = data.lat || '-';
                                document.getElementById('detail-long').textContent = data.long || '-';
                                document.getElementById('detail-thoroughfare').textContent = data.thoroughfare || '-';
                                document.getElementById('detail-subLocality').textContent = data.subLocality || '-';
                                document.getElementById('detail-locality').textContent = data.locality || '-';
                                document.getElementById('detail-subAdmin').textContent = data.subAdmin || '-';
                                document.getElementById('detail-adminArea').textContent = data.adminArea || '-';
                                document.getElementById('detail-postalCode').textContent = data.postalCode || '-';
                                
                                // Handle image
                                const imageElement = document.getElementById('detail-image');
                                const imagePlaceholder = document.getElementById('image-placeholder');
                                
                                if (data.image_url) {
                                    imageElement.src = data.image_url;
                                    imageElement.style.display = 'block';
                                    imagePlaceholder.style.display = 'none';
                                } else {
                                    imageElement.style.display = 'none';
                                    imagePlaceholder.style.display = 'block';
                                }
                                
                                // Center map on this location if coordinates are available
                                if (data.lat && data.long) {
                                    map.flyTo({
                                        center: [data.long, data.lat],
                                        zoom: 15,
                                        essential: true
                                    });
                                    
                                    // Scroll to map container when a row is clicked
                                    document.querySelector('#map').scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                                
                                // No need to scroll to detail container as we now scroll to map
                                
                            } catch (error) {
                                console.error('Error fetching data details:', error);
                                alert('Failed to load data details. Please try again.');
                            }
                        });
                    });
                    
                    // Fetch all data points for the map
                    const response = await fetch('/api/data');
                    if (!response.ok) {
                        throw new Error(`Error: ${response.statusText}`);
                    }
                    
                    const allData = await response.json();
                    
                    // Add markers for each data point
                    allData.forEach(point => {
                        if (point.lat && point.long) {
                            // Create popup content
                            const popupContent = `
                                <div class="map-popup-content">
                                    <h5>${point.uploader || 'Unknown'}</h5>
                                    <p><strong>Jumlah Spanduk:</strong> ${point.spandukCount || '0'}</p>
                                    <p><strong>Area:</strong> ${point.thoroughfare || '-'}</p>
                                
                                    <p><strong>Waktu:</strong> ${point.createdAt || '-'}</p>
                                    <p><strong>Koordinat:</strong> ${point.lat}, ${point.long}</p>
                                </div>
                            `;
                            
                            // Create popup
                            const popup = new mapboxgl.Popup({ offset: 25 })
                                .setHTML(popupContent);
                            
                            // Create marker
                            const marker = new mapboxgl.Marker()
                                .setLngLat([point.long, point.lat])
                                .setPopup(popup)
                                .addTo(map);
                            
                            markers.push(marker);
                            
                            // Add click event to marker that shows details and scrolls to map
                            marker.getElement().addEventListener('click', async () => {
                                try {
                                    // Show loading state
                                    const detailContainer = document.getElementById('detailContainer');
                                    detailContainer.style.display = 'flex';
                                    detailContainer.innerHTML = '<div class="text-center w-100"><div class="spinner-border" role="status"></div></div>';
                                    
                                    // Fetch data using coordinates
                                    const response = await fetch(`/api/data/coordinates?lat=${point.lat}&long=${point.long}`);
                                    if (!response.ok) {
                                        throw new Error(`Error: ${response.statusText}`);
                                    }
                                    
                                    const data = await response.json();
                                    
                                    // Update detail container with fetched data
                                    detailContainer.innerHTML = `
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="bg-white p-3 rounded shadow-sm">
                                                    <div class="row small g-2">
                                                    <div class="col-6 col-md-3"><span class="text-muted">Uploader:</span> <span id="detail-uploader">' + (data.uploader || '-') + '</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Kelompok:</span> <span id="detail-group">${data.group || '-'}</span></div>
                                                     <div class="col-6 col-md-3"><span class="text-muted">Jml Spanduk:</span> <span id="detail-spandukCount">${data.spandukCount || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Waktu:</span> <span id="detail-createdAt">${data.createdAt || '-'}</span></div>
                                                     <div class="col-6 col-md-3"><span class="text-muted">Lat:</span> <span id="detail-lat">${data.lat || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Long:</span> <span id="detail-long">${data.long || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Area 1:</span> <span id="detail-thoroughfare">${data.thoroughfare || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Area 2:</span> <span id="detail-subLocality">${data.subLocality || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Area 3:</span> <span id="detail-locality">${data.locality || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Area 4:</span> <span id="detail-subAdmin">${data.subAdmin || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Area 5:</span> <span id="detail-adminArea">${data.adminArea || '-'}</span></div>
                                                    <div class="col-6 col-md-3"><span class="text-muted">Kode Pos:</span> <span id="detail-postalCode">${data.postalCode || '-'}</span></div>
                                                </div>
                                            </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="bg-white p-3 rounded shadow-sm h-100">
                                                    <div id="image-placeholder" class="d-flex align-items-center justify-content-center h-100">
                                                        <div class="text-muted">No image available</div>
                                                    </div>
                                                    <img id="detail-image" src="" class="w-100" style="height: 100%; width: auto; object-fit: contain; display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    
                                    // Handle image with proper loading states
                                    const imageElement = document.getElementById('detail-image');
                                    const imagePlaceholder = document.getElementById('image-placeholder');
                                    
                                    imageElement.style.display = 'none';
                                    imagePlaceholder.style.display = 'block';
                                    imagePlaceholder.innerHTML = '<div class="text-muted">No image available</div>';
                                    
                                    if (data.image_url) {
                                        imagePlaceholder.innerHTML = '<div class="spinner-border" role="status"></div>';
                                        
                                        // Preload image
                                        const img = new Image();
                                        img.onload = function() {
                                            imageElement.src = data.image_url;
                                            imageElement.style.display = 'block';
                                            imagePlaceholder.style.display = 'none';
                                        };
                                        img.onerror = function() {
                                            imageElement.style.display = 'none';
                                            imagePlaceholder.style.display = 'block';
                                            imagePlaceholder.innerHTML = '<div class="text-muted">Failed to load image</div>';
                                        };
                                        img.src = data.image_url;
                                    }
                                    
                                    // Center map
                                    if (data.lat && data.long) {
                                        map.flyTo({
                                            center: [data.long, data.lat],
                                            zoom: 15,
                                            essential: true
                                        });
                                    }
                                    
                                    // Scroll to map container
                                    document.querySelector('#map').scrollIntoView({ behavior: 'smooth', block: 'start' });
                                    
                                } catch (error) {
                                    console.error('Error fetching data details:', error);
                                    const detailContainer = document.getElementById('detailContainer');
                                    detailContainer.innerHTML = '<div class="alert alert-danger">Failed to load data details. Please try again.</div>';
                                }
                            });
                        }
                    });
                    
                    // Fit map to markers if there are any
                    if (markers.length > 0) {
                        const bounds = new mapboxgl.LngLatBounds();
                        markers.forEach(marker => {
                            bounds.extend(marker.getLngLat());
                        });
                        map.fitBounds(bounds, { padding: 50 });
                    }
                    
                } catch (error) {
                    console.error('Error loading data points:', error);
                }
            }
            
            // Load data points when map is loaded
            map.on('load', loadDataPoints);
        } catch (error) {
            console.error('Error initializing map:', error);
            document.getElementById('map').innerHTML = 
                `<div class="alert alert-danger">Error initializing map: ${error.message}</div>`;
        }
    });
    // Function to open image modal
    function openImageModal(imageSrc) {
        document.getElementById('modal-image').src = imageSrc;
        var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }
</script>
@endsection
