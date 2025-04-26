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

<div class="container">
    <h1>Welcome, {{ Auth::user()->full_name }}!</h1>
    <p>Welcome to your dashboard!</p>
</div>
@endsection