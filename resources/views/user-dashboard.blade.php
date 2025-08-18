@extends('layouts/layoutMaster')

@section('title', 'Dashboard Pengguna')

@section('page-script')
    <script>
        // NOTE: Agar script WebSocket berfungsi, Anda perlu menginstal dan mengkonfigurasi
        // Laravel Echo dan Pusher.js (atau driver Reverb) di file bootstrap.js Anda.

        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika untuk update gambar (tetap sama) ---
            function updateCameraFeed(cameraCard) {
                const imgElement = cameraCard.querySelector('.camera-feed-image');
                const timestampElement = cameraCard.querySelector('.camera-timestamp');
                const cameraId = imgElement.dataset.cameraId;

                if (!cameraId) return;

                fetch(`/api/cameras/${cameraId}/latest-image`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (imgElement.src !== data.image_url) {
                                imgElement.src = data.image_url;
                            }
                            if (timestampElement) {
                                timestampElement.textContent = 'Update: ' + data.captured_at;
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching image for camera ' + cameraId + ':', error));
            }

            const cameraCards = document.querySelectorAll('.camera-card');
            cameraCards.forEach(updateCameraFeed);
            setInterval(() => {
                cameraCards.forEach(updateCameraFeed);
            }, 5000);

            // --- [BARU] Logika WebSocket untuk status online/offline ---
            if (typeof window.Echo !== 'undefined') {
                cameraCards.forEach(card => {
                    const statusBadge = card.querySelector('.camera-status-badge');
                    const channelId = statusBadge.dataset.websocketChannelId;
                    let onlineTimeout;

                    if (channelId) {
                        // Dengarkan event di channel privat milik kamera
                        window.Echo.private(channelId) // Menggunakan private channel
                            .listen('.camera.online', (e) => {
                                // Ubah status menjadi Aktif
                                statusBadge.classList.remove('bg-label-danger');
                                statusBadge.classList.add('bg-label-success');
                                statusBadge.textContent = 'Aktif';

                                // Hapus timeout sebelumnya
                                clearTimeout(onlineTimeout);

                                // Atur timeout baru. Jika tidak ada pesan "online" lagi dalam 30 detik,
                                // anggap kamera offline.
                                onlineTimeout = setTimeout(() => {
                                    statusBadge.classList.remove('bg-label-success');
                                    statusBadge.classList.add('bg-label-danger');
                                    statusBadge.textContent = 'Offline';
                                }, 30000); // Timeout 30 detik
                            });
                    }
                });
            } else {
                console.warn('Laravel Echo tidak terkonfigurasi. Status kamera tidak akan real-time.');
            }
        });
    </script>
@endsection

@section('content')
    {{-- Header Dashboard --}}
    <h4>Dashboard Pemantauan Kamera</h4>
    <p>Selamat datang kembali, <strong>{{ auth()->user()->name ?? 'User' }}</strong>.</p>

    {{-- Kartu Statistik Pengguna --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Kamera Anda</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalCameras }}</h3>
                            </div>
                            <small>Semua kamera milik Anda</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-camera ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Kamera Aktif</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $activeCameras }}</h3>
                            </div>
                            <small>Kamera Anda yang online</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti ti-video ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Tampilan Kamera --}}
    <h5 class="mb-4">Pratinjau Kamera Anda</h5>
    <div class="row g-4">
        @forelse($cameras as $camera)
            <div class="col-md-6 col-lg-4 camera-card">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">{{ $camera->name }}</h5>
                        {{-- FIX: Tambahkan data-websocket-channel-id --}}
                        <span
                            class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }} camera-status-badge"
                            data-websocket-channel-id="{{ $camera->websocket_channel_id }}">
                            {{ $camera->is_active ? 'Aktif' : 'Offline' }}
                        </span>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $latestImage = $camera->imageRecords->first();
                        @endphp
                        <img class="img-fluid rounded camera-feed-image" id="camera-feed-{{ $camera->id }}"
                            data-camera-id="{{ $camera->id }}" style="height: 180px; width: 100%; object-fit: cover;"
                            src="{{ $latestImage ? \Illuminate\Support\Facades\Storage::url($latestImage->path) : 'https://placehold.co/600x400/293445/FFFFFF?text=Loading...' }}"
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
                        <h5 class="card-title">Anda Belum Memiliki Kamera</h5>
                        <p class="card-text">Hubungi admin untuk mendaftarkan perangkat kamera baru.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
