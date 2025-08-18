@extends('layouts/layoutMaster')

@section('title', 'Tambahkan Kamera Baru')

@section('content')
    <h4 class="mb-4">Tambahkan Perangkat Kamera</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">Sinkronkan Perangkat</h5>
                <div class="card-body">
                    <p>Masukkan <strong>Device ID</strong> yang Anda dapatkan dari admin untuk menghubungkan perangkat
                        kamera ke akun Anda.</p>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('user.cameras.link.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="device_id" class="form-label">Device ID</label>
                            <input type="text" class="form-control @error('device_id') is-invalid @enderror"
                                id="device_id" name="device_id" value="{{ old('device_id') }}"
                                placeholder="Contoh: a1b2c3d4-e5f6-7890-1234-567890abcdef" required>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ti ti-link me-1"></i> Hubungkan Kamera
                        </button>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
