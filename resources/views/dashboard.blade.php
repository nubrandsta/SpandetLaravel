@extends('layouts.app')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-light">Logout</button>
        </form>
    </div>
</nav>
<div class="container mb-4">
    <h1 class="h2 mb-3">Welcome, {{ Auth::user()->full_name }}!</h1>
</div>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light sidebar">
            <div class="list-group mt-3">
                <a href="#" class="list-group-item list-group-item-action">User Management</a>
                <a href="#" class="list-group-item list-group-item-action">Group Management</a>
                <a href="#" class="list-group-item list-group-item-action">Data Management</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ps-md-4">
            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
                <h4>Data Table</h4>
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi-arrow-clockwise"></i> Refresh
                    </a>
                </div>
            </div>

            <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Uploader</th>
                        <th>Group</th>
                        <th>Spanduk Count</th>
                        <th>Thoroughfare</th>
                        <th>Sub-Locality</th>
                        <th>Locality</th>
                        <th>Sub-Admin</th>
                        <th>Admin Area</th>
                        <th>Postal Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td>{{ $item->uploader }}</td>
                        <td>{{ $item->group }}</td>
                        <td>{{ $item->spandukCount }}</td>
                        <td>{{ $item->thoroughfare }}</td>
                        <td>{{ $item->sublocality }}</td>
                        <td>{{ $item->locality }}</td>
                        <td>{{ $item->subadmin }}</td>
                        <td>{{ $item->adminArea }}</td>
                        <td>{{ $item->postalcode }}</td>
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
@endsection