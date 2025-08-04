@extends('layouts/layoutMaster')

@section('title', 'Notifikasi & Peringatan')

@section('content')
    <h4 class="mb-4">Notifikasi & Peringatan Sistem</h4>

    <div class="card">
        <h5 class="card-header">Peringatan Kamera Offline</h5>
        <div class="list-group list-group-flush">
            @forelse($offlineCameras as $camera)
                <div class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="ti ti-alert-triangle-filled ti-lg me-3 text-danger"></i>
                    <div class="d-flex flex-column">
                        <h6 class="mb-0">Kamera '{{ $camera->name }}' Offline</h6>
                        <small class="text-muted">Tidak ada sinyal diterima dalam 10 menit terakhir.</small>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center">
                    <div class="d-flex flex-column align-items-center my-3">
                        <i class="ti ti-circle-check-filled ti-lg mb-2 text-success"></i>
                        <h6 class="mb-0">Semua Kamera Online</h6>
                        <small class="text-muted">Tidak ada peringatan saat ini.</small>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Anda bisa menambahkan card lain di sini untuk jenis notifikasi berbeda --}}
    {{-- Contoh: Notifikasi Deteksi Penting --}}
    <div class="card mt-4">
        <h5 class="card-header">Notifikasi Lainnya</h5>
        <div class="list-group list-group-flush">
            <div class="list-group-item text-center">
                <p class="text-muted my-3">Tidak ada notifikasi lainnya.</p>
            </div>
        </div>
    </div>
@endsection
