@extends('layouts/layoutMaster')

@section('title', 'Dashboard Realtime Kamera')

@section('vendor-style')
<style>
    .group-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .group-header h5 { margin: 0; color: white; font-weight: 600; }
    .camera-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .camera-card:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .camera-feed-image {
        transition: opacity 0.3s ease;
        background-color: #1a1a1a;
        min-height: 200px;
    }
    /* Indikator Koneksi */
    #connection-status {
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 20px;
    }
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusEl = document.getElementById('connection-status');
        const statusText = document.getElementById('status-text');

        /**
         * 1. Fungsi Utama Update UI (Tanpa Refresh)
         */
        function updateCameraUI(event) {
            const camera = event.camera;
            const data = event.data;

            const card = document.querySelector(`.camera-card[data-id="${camera.id}"]`);
            if (!card) return;

            const imgElement = card.querySelector('.camera-feed-image');
            const statusBadge = card.querySelector('.status-badge');
            const timestampElement = card.querySelector('.camera-timestamp');

            // A. Update Gambar Realtime
            if (data.type === 'image' && imgElement) {
                // Tambahkan timestamp di URL untuk menghindari cache browser
                const newSrc = data.image_url + (data.image_url.includes('?') ? '&' : '?') + 't=' + new Date().getTime();
                imgElement.src = newSrc;

                if (timestampElement) {
                    timestampElement.textContent = 'Update: ' + (data.captured_at || 'Baru saja');
                }

                // Efek visual flash
                imgElement.style.opacity = '0.5';
                setTimeout(() => imgElement.style.opacity = '1', 300);
            }

            // B. Update Status Realtime (Online/Offline)
            if (data.type === 'status' || data.type === 'telemetry') {
                const isOnline = data.status_message === 'online' || data.type === 'telemetry';

                if (statusBadge) {
                    statusBadge.className = `status-badge badge ${isOnline ? 'bg-label-success' : 'bg-label-danger'}`;
                    statusBadge.textContent = isOnline ? 'Online' : 'Offline';
                }
                // Update counter setiap kali ada perubahan status masuk
                recalculateActiveStats();
            }
        }

        /**
         * Fungsi untuk menghitung ulang jumlah kamera aktif berdasarkan UI
         */
        function recalculateActiveStats() {
            const activeCount = document.querySelectorAll('.status-badge.bg-label-success').length;
            const activeCounterEl = document.getElementById('active-camera-counter');
            if (activeCounterEl) {
                activeCounterEl.textContent = activeCount;
            }
        }

        /**
         * 2. Inisialisasi Laravel Echo
         */
        function initEcho() {
            if (typeof Echo !== 'undefined') {
                statusText.textContent = 'Menghubungkan...';

                Echo.channel('cameras')
                    .listen('.CameraUpdated', (e) => {
                        console.log('Update diterima:', e);
                        updateCameraUI(e);
                    });

                Echo.connector.pusher.connection.bind('connected', () => {
                    statusEl.className = 'badge bg-label-success';
                    statusText.textContent = 'Terhubung (Realtime)';
                });

                Echo.connector.pusher.connection.bind('disconnected', () => {
                    statusEl.className = 'badge bg-label-danger';
                    statusText.textContent = 'Terputus';
                });
            } else {
                statusEl.className = 'badge bg-label-warning';
                statusText.textContent = 'Echo Belum Dimuat';
            }
        }

        // Jalankan inisialisasi
        initEcho();
        // Sync angka statistik awal berdasarkan badge yang ada di layar
        recalculateActiveStats();

        const filter = document.getElementById('groupFilter');
        if (filter) filter.addEventListener('change', () => filter.form.submit());
    });
</script>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary"><i class="ti ti-video me-2"></i>Dashboard Pemantauan</h4>
            <div id="connection-status" class="badge bg-label-secondary">
                <i class="ti ti-circle-filled ti-xs me-1"></i>
                <span id="status-text">Memeriksa Koneksi...</span>
            </div>
        </div>
        @role('admin')
        <a href="{{ route('admin.camera-groups.index') }}" class="btn btn-primary">
            <i class="ti ti-settings me-1"></i> Kelola Grup
        </a>
        @endrole
    </div>

    {{-- Statistik --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-camera ti-md"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalCameras ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Total Kamera</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="ti ti-broadcast ti-md"></i></span>
                        </div>
                        {{-- ID ini digunakan JS untuk update angka tanpa refresh --}}
                        <h4 class="ms-1 mb-0 text-success" id="active-camera-counter">0</h4>
                    </div>
                    <p class="mb-1">Kamera Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="ti ti-users ti-md"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalUsers ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Total Pengguna</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    @if(count($groups) > 1)
    <div class="card mb-4 border-0 shadow-none bg-transparent">
        <div class="card-body p-0">
            <form method="POST" action="{{ url('/dashboard/groups') }}">
                @csrf
                <div class="d-flex align-items-center">
                    <div style="min-width: 250px;">
                        <select name="group" id="groupFilter" class="form-select border-0 shadow-sm">
                            @foreach($groups as $name)
                                <option value="{{ $name }}" {{ $currentGroup == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @php
        $groupedCameras = $cameras->groupBy(fn($item) => $item->group ? $item->group->name : 'Tanpa Grup');
        $showHeaders = $currentGroup == 'Semua Kamera';
    @endphp

    @forelse($groupedCameras as $groupName => $items)
        <div class="mb-5">
            @if($showHeaders)
            <div class="group-header">
                <h5><i class="ti ti-folder-check me-2"></i>{{ $groupName }}</h5>
                <span class="badge bg-white text-primary">{{ $items->count() }} Kamera</span>
            </div>
            @endif

            <div class="row g-4">
                @foreach($items as $camera)
                    <div class="col-md-6 col-lg-4 camera-card" data-id="{{ $camera->id }}">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header d-flex justify-content-between align-items-center py-3">
                                <h6 class="mb-0 fw-bold">{{ $camera->name }}</h6>
                                <span class="status-badge badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $camera->is_active ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                            <div class="card-body p-0 bg-dark" style="height: 220px;">
                                @php $latest = $camera->imageRecords->first(); @endphp
                                <img class="camera-feed-image w-100 h-100"
                                     style="object-fit: contain;"
                                     src="{{ $latest ? \Illuminate\Support\Facades\Storage::url($latest->path) : 'https://placehold.co/600x400?text=Menunggu+Koneksi' }}"
                                     onerror="this.src='https://placehold.co/600x400?text=Gambar+Gagal+Dimuat'">
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center py-2 bg-light">
                                <small class="text-muted camera-timestamp">
                                    {{ $latest ? $latest->captured_at->diffForHumans() : 'Belum ada data' }}
                                </small>
                                <a href="{{ route('log.history.explorer', $camera->id) }}" class="btn btn-sm btn-outline-primary">Riwayat</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <p class="text-muted">Tidak ada kamera ditemukan.</p>
        </div>
    @endforelse
@endsection
