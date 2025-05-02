@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center mb-4">Ubah Password</h2>
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.change') }}">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                           id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="old_password" class="form-label">Password Lama</label>
                    <input type="password" class="form-control @error('old_password') is-invalid @enderror" 
                           id="old_password" name="old_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                           id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Ulangi Password Baru</label>
                    <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                           id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Ubah Password</button>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">Kembali ke Login</a>
            </form>
        </div>
    </div>
</div>
@endsection