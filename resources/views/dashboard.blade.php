@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('page-script')
    <script>
        // Script ini bisa digunakan nanti untuk memperbarui feed gambar secara real-time
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Dashboard loaded. Real-time feed updater can be initialized here.");
        });
    </script>
@endsection

@section('content')
    {{-- Header Dashboard --}}
    <h4>Dashboard Pemantauan Kamera</h4>
    <p>Selamat datang kembali, <strong>{{ auth()->user()->name ?? 'User' }}</strong>.</p>

    {{-- Kartu Statistik --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Kamera</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalCameras ?? 0 }}</h3>
                            </div>
                            <small>Semua kamera terdaftar</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-camera ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Kamera Aktif</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $activeCameras ?? 0 }}</h3>
                            </div>
                            <small>Kamera yang sedang online</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti ti-video ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Pengguna</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalUsers ?? 0 }}</h3>
                            </div>
                            <small>Total pengguna terdaftar</small>
                        </div>
                        <span class="badge bg-label-secondary rounded p-2">
                            <i class="ti ti-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Tampilan Kamera --}}
    <h5 class="mb-4">Pratinjau Kamera</h5>
    <div class="row g-4">
        @forelse($cameras as $camera)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">{{ $camera->name }}</h5>
                        @if ($camera->is_active)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-danger">Offline</span>
                        @endif
                    </div>
                    <div class="card-body text-center">
                        @php
                            $latestImage = $camera->imageRecords->first();
                        @endphp
                        <img class="img-fluid rounded" style="height: 180px; width: 100%; object-fit: cover;"
                            src="{{ $latestImage ? \Illuminate\Support\Facades\Storage::url($latestImage->path) : 'https://placehold.co/600x400/293445/FFFFFF?text=No+Image' }}"
                            alt="Live feed untuk {{ $camera->name }}">
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted camera-timestamp">
                            Update: {{ $latestImage ? $latestImage->captured_at->format('H:i:s') . ' WIB' : 'N/A' }}
                        </small>
                        <a href="{{ route('log.history.folders', $camera->id) }}"
                            class="btn btn-sm btn-outline-primary">Riwayat</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Belum Ada Kamera</h5>
                        <p class="card-text">Silakan daftarkan perangkat kamera baru untuk memulai pemantauan.</p>
                        {{-- FIX: Hanya tampilkan tombol ini untuk pengguna dengan peran 'admin' --}}
                        @role('admin')
                            <a href="{{ route('admin.cameras.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Tambah Kamera
                            </a>
                        @endrole
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
