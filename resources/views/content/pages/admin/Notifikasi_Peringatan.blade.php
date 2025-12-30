@extends('layouts/layoutMaster')

@section('title', 'Notifikasi & Peringatan')

@section('vendor-style')
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .notification-item.alert-offline {
        border-left-color: #ea5455;
        background-color: rgba(234, 84, 85, 0.05);
    }
    .notification-item.alert-online {
        border-left-color: #28c76f;
    }
    .status-pulse {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(234, 84, 85, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(234, 84, 85, 0); }
        100% { box-shadow: 0 0 0 0 rgba(234, 84, 85, 0); }
    }
    .empty-state {
        padding: 40px 20px;
        text-align: center;
    }
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const offlineContainer = document.getElementById('offline-cameras-list');
        const statsOfflineCount = document.getElementById('stats-offline-count');

        function updateNotifications() {
            fetch('/api/camera-statuses')
                .then(response => response.json())
                .then(data => {
                    let offlineContent = '';
                    let offlineCount = 0;

                    // Filter kamera yang offline
                    Object.entries(data).forEach(([id, info]) => {
                        // Mengasumsikan info adalah objek { name: string, is_active: bool }
                        const isActive = (typeof info === 'object') ? info.is_active : info;

                        if (!isActive) {
                            offlineCount++;
                            offlineContent += `
                                <div class="list-group-item list-group-item-action d-flex align-items-center notification-item alert-offline py-3">
                                    <div class="status-pulse bg-danger"></div>
                                    <i class="ti ti-alert-triangle-filled ti-lg me-3 text-danger"></i>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold">Kamera ID #${id} Terputus</h6>
                                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Baru Saja</small>
                                        </div>
                                        <small class="text-muted">Perangkat tidak merespons heartbeat dalam 15 detik terakhir. Mohon periksa koneksi daya atau internet di lokasi.</small>
                                    </div>
                                    <a href="/admin/cameras/${id}/edit" class="btn btn-sm btn-label-secondary ms-3">Cek</a>
                                </div>
                            `;
                        }
                    });

                    // Update UI Offline
                    if (offlineCount > 0) {
                        offlineContainer.innerHTML = offlineContent;
                        statsOfflineCount.textContent = offlineCount;
                        statsOfflineCount.className = 'badge bg-danger ms-2';
                    } else {
                        offlineContainer.innerHTML = `
                            <div class="empty-state">
                                <i class="ti ti-circle-check-filled ti-xl mb-2 text-success"></i>
                                <h6 class="mb-0 text-success">Semua Kamera Terhubung</h6>
                                <small class="text-muted">Sistem berjalan optimal tanpa kendala koneksi.</small>
                            </div>
                        `;
                        statsOfflineCount.textContent = '0';
                        statsOfflineCount.className = 'badge bg-label-secondary ms-2';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Polling setiap 5 detik agar responsif terhadap perubahan status kamera
        setInterval(updateNotifications, 5000);
        updateNotifications();
    });
</script>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Notifikasi & Peringatan</h4>
            <p class="text-muted mb-0">Pantau kesehatan sistem dan anomali deteksi secara real-time.</p>
        </div>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="ti ti-refresh me-1"></i> Perbarui Manual
        </button>
    </div>

    <div class="row">
        {{-- Ringkasan Status --}}
        <div class="col-md-12 mb-4">
            <div class="card bg-label-primary border-0 shadow-none">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-primary"><i class="ti ti-info-circle ti-md"></i></span>
                        </div>
                        <div>
                            <h5 class="mb-0 text-primary">Status Infrastruktur</h5>
                            <small>Kamera yang membutuhkan perhatian: <span id="stats-offline-count" class="badge bg-label-secondary ms-2">...</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log Peringatan Offline --}}
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom py-3">
                    <h5 class="mb-0"><i class="ti ti-wifi-off me-2 text-danger"></i>Peringatan Kamera Terputus</h5>
                    <span class="text-muted small">Live Update Aktif</span>
                </div>
                <div class="list-group list-group-flush" id="offline-cameras-list">
                    {{-- Konten diisi secara dinamis oleh JavaScript --}}
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted small">Menyinkronkan status perangkat...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Notifikasi Deteksi --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="mb-0"><i class="ti ti-brain me-2 text-info"></i>Deteksi ML Penting</h5>
                </div>
                <div class="card-body px-0 py-2">
                    <div class="list-group list-group-flush">
                        {{-- Contoh notifikasi deteksi --}}
                        <div class="list-group-item border-0">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-label-info p-2 me-3"><i class="ti ti-user"></i></span>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold small">Orang Terdeteksi</p>
                                    <small class="text-muted d-block">Kamera Depan • 2 Menit Lalu</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 text-center py-4">
                            {{-- PERBAIKAN: Update nama rute sesuai konfigurasi baru Anda --}}
                            <p class="text-muted small mb-0">Lihat selengkapnya di <a href="{{ route('ml.ml.detection-log.index') }}" class="text-primary fw-bold">Log Deteksi</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-dark text-white shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <i class="ti ti-shield-check ti-xl mb-3 text-success"></i>
                    <h6>Keamanan Terjamin</h6>
                    <p class="small opacity-75">Semua data gambar disimpan aman di MinIO dan dienkripsi saat pengiriman.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
