@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk memperbarui satu feed kamera
            function updateCameraFeed(cameraCard) {
                const imgElement = cameraCard.querySelector('.camera-feed-image');
                const timestampElement = cameraCard.querySelector('.camera-timestamp');
                const statusBadge = cameraCard.querySelector('.camera-status-badge');
                const cameraId = imgElement.dataset.cameraId;

                if (!cameraId) return;

                // Panggil API untuk mendapatkan gambar terbaru
                fetch(`/api/cameras/${cameraId}/latest-image`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('API request failed');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update sumber gambar hanya jika berbeda untuk mengurangi kedipan
                            if (imgElement.src !== data.image_url) {
                                imgElement.src = data.image_url;
                            }
                            // Update timestamp
                            if (timestampElement) {
                                timestampElement.textContent = 'Update: ' + data.captured_at;
                            }
                            // Update status menjadi aktif jika berhasil
                            if (statusBadge && !statusBadge.classList.contains('bg-label-success')) {
                                statusBadge.classList.remove('bg-label-danger');
                                statusBadge.classList.add('bg-label-success');
                                statusBadge.textContent = 'Aktif';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data for camera ' + cameraId + ':', error);
                    });
            }

            // Dapatkan semua kartu kamera
            const cameraCards = document.querySelectorAll('.camera-card');

            // Panggil update untuk semua kamera saat halaman dimuat
            cameraCards.forEach(updateCameraFeed);

            // Atur interval untuk memperbarui semua kamera setiap 5 detik
            setInterval(() => {
                cameraCards.forEach(updateCameraFeed);
            }, 5000); // 5000 ms = 5 detik
        });
    </script>
@endsection

@section('content')
    {{-- Header Dashboard --}}
    <h4>Dashboard Pemantauan Kamera</h4>
    <p>Selamat datang kembali, <strong>{{ auth()->user()->name ?? 'User' }}</strong>.</p>

    {{-- Kartu Statistik --}}
    <div class="row g-4 mb-4">
        {{-- Konten kartu statistik tetap sama --}}
    </div>

    {{-- Grid Tampilan Kamera --}}
    <h5 class="mb-4">Pratinjau Kamera</h5>
    <div class="row g-4">
        @forelse($cameras as $camera)
            {{-- Tambahkan class 'camera-card' untuk target JavaScript --}}
            <div class="col-md-6 col-lg-4 camera-card">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">{{ $camera->name }}</h5>
                        {{-- Tambahkan class 'camera-status-badge' --}}
                        <span
                            class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }} camera-status-badge">
                            {{ $camera->is_active ? 'Aktif' : 'Offline' }}
                        </span>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $latestImage = $camera->imageRecords->first();
                        @endphp
                        {{-- Tambahkan id unik dan data-camera-id untuk target JavaScript --}}
                        <img class="img-fluid rounded camera-feed-image" id="camera-feed-{{ $camera->id }}"
                            data-camera-id="{{ $camera->id }}" style="height: 180px; width: 100%; object-fit: cover;"
                            src="{{ $latestImage ? \Illuminate\Support\Facades\Storage::url($latestImage->path) : 'https://placehold.co/600x400/293445/FFFFFF?text=Loading...' }}"
                            alt="Live feed untuk {{ $camera->name }}">
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        {{-- Tambahkan class 'camera-timestamp' --}}
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
                        <a href="{{ route('admin.cameras.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Tambah Kamera
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
