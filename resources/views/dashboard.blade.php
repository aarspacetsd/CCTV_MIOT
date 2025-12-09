@extends('layouts/layoutMaster')

@section('title', 'Dashboard Admin')

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

    .group-header h5 {
        margin: 0;
        color: white;
        font-weight: 600;
    }

    .group-actions {
        display: flex;
        gap: 8px;
    }

    .group-filter-container {
        background: white;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .group-filter-label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .camera-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .camera-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .subgroup-container {
        padding-left: 20px;
        margin-top: 8px;
    }

    .subgroup-header {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 10px 16px;
        margin-bottom: 12px;
        border-radius: 4px;
    }

    .subgroup-header h6 {
        margin: 0;
        color: #495057;
        font-weight: 600;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
    }

    .toggle-icon.collapsed {
        transform: rotate(-90deg);
    }
</style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika untuk update gambar ---
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

            const allCameraCards = document.querySelectorAll('.camera-card');
            allCameraCards.forEach(updateCameraFeed);
            setInterval(() => {
                allCameraCards.forEach(updateCameraFeed);
            }, 5000);

            // --- Polling status kamera setiap 10 detik ---
            function checkCameraStatuses() {
                fetch('/api/camera-statuses')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(statuses => {
                        for (const cameraId in statuses) {
                            const statusBadge = document.getElementById(`camera-status-${cameraId}`);
                            if (statusBadge) {
                                const isActive = statuses[cameraId];
                                if (isActive) {
                                    statusBadge.classList.remove('bg-label-danger');
                                    statusBadge.classList.add('bg-label-success');
                                    statusBadge.textContent = 'Aktif';
                                } else {
                                    statusBadge.classList.remove('bg-label-success');
                                    statusBadge.classList.add('bg-label-danger');
                                    statusBadge.textContent = 'Offline';
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching camera statuses:', error);
                    });
            }

            checkCameraStatuses();
            setInterval(checkCameraStatuses, 10000);

            // --- Toggle collapse untuk grup ---
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('.toggle-icon');
                    if (icon) {
                        icon.classList.toggle('collapsed');
                    }
                });
            });

            // --- Auto-submit form saat pilihan grup berubah ---
            const groupSelect = document.getElementById('groupFilter');
            if (groupSelect) {
                groupSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    </script>
@endsection

@section('content')
    {{-- Header Dashboard --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Dashboard Pemantauan Kamera</h4>
            <p class="mb-0">Selamat datang kembali, <strong>{{ auth()->user()->name ?? 'User' }}</strong>.</p>
        </div>
        @role('admin')
        <div>
            <a href="{{ route('admin.camera-groups.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-settings me-1"></i> Kelola Grup
            </a>
        </div>
        @endrole
    </div>

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

    {{-- Filter Grup --}}
    @if(count($groups ?? []) > 1)
    <div class="group-filter-container">
        <form method="POST" action="{{ url('/dashboard/groups') }}" id="groupFilterForm">
            @csrf
            <label class="group-filter-label">
                <i class="ti ti-filter me-1"></i> Filter Berdasarkan Grup
            </label>
            <div class="row">
                <div class="col-md-6">
                    <select name="group" id="groupFilter" class="form-select">
                        @foreach($groups as $group)
                            <option value="{{ $group }}" {{ $currentGroup == $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
    @endif

    {{-- Pratinjau Kamera dengan Grouping --}}
    <h5 class="mb-4">
        <i class="ti ti-video me-2"></i>Pratinjau Kamera
        @if($currentGroup != 'Semua Kamera')
            <span class="badge bg-primary ms-2">{{ $currentGroup }}</span>
        @endif
    </h5>

    @php
        // Grouping kamera berdasarkan group_name
        $groupedCameras = $cameras->groupBy('group_name');
        // Jika filter "Semua Kamera", tampilkan dengan grouping
        // Jika filter spesifik, tampilkan tanpa header grup (sudah terfilter)
        $showGroupHeaders = $currentGroup == 'Semua Kamera';
    @endphp

    @if($cameras->count() > 0)
        @if($showGroupHeaders)
            {{-- Tampilan dengan Group Headers --}}
            @foreach($groupedCameras as $groupName => $groupCameras)
                <div class="mb-4">
                    <div class="group-header">
                        <h5>
                            <i class="ti ti-folder me-2"></i>
                            {{ $groupName ?: 'Tanpa Grup' }}
                            <span class="badge bg-white text-primary ms-2">{{ $groupCameras->count() }}</span>
                        </h5>
                        <div class="group-actions">
                            <button class="btn btn-sm btn-light" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#group-{{ \Illuminate\Support\Str::slug($groupName) }}"
                                    aria-expanded="true">
                                <i class="ti ti-chevron-down toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="collapse show" id="group-{{ \Illuminate\Support\Str::slug($groupName) }}">
                        <div class="row g-4">
                            @foreach($groupCameras as $camera)
                                <div class="col-md-6 col-lg-4 camera-card">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="card-title mb-0">{{ $camera->name }}</h5>
                                            <span class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                                                id="camera-status-{{ $camera->id }}">
                                                {{ $camera->is_active ? 'Aktif' : 'Offline' }}
                                            </span>
                                        </div>
                                        <div class="card-body text-center">
                                            @php
                                                $latestImage = $camera->imageRecords->first();
                                            @endphp
                                            <img class="img-fluid rounded camera-feed-image"
                                                style="height: 180px; width: 100%; object-fit: cover;"
                                                id="camera-feed-{{ $camera->id }}"
                                                data-camera-id="{{ $camera->id }}"
                                                src="{{ $latestImage ? \Illuminate\Support\Facades\Storage::url($latestImage->path) : 'https://placehold.co/600x400/293445/FFFFFF?text=No+Image' }}"
                                                alt="Live feed untuk {{ $camera->name }}">
                                        </div>
                                        <div class="card-footer d-flex justify-content-between align-items-center">
                                            <small class="text-muted camera-timestamp">
                                                Update: {{ $latestImage ? $latestImage->captured_at->format('H:i:s') . ' WIB' : 'N/A' }}
                                            </small>
                                            <a href="{{ route('log.history.explorer', $camera->id) }}"
                                                class="btn btn-sm btn-outline-primary">Riwayat</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Tampilan tanpa Group Headers (sudah terfilter) --}}
            <div class="row g-4">
                @foreach($cameras as $camera)
                    <div class="col-md-6 col-lg-4 camera-card">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title mb-0">{{ $camera->name }}</h5>
                                <span class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                                    id="camera-status-{{ $camera->id }}">
                                    {{ $camera->is_active ? 'Aktif' : 'Offline' }}
                                </span>
                            </div>
                            <div class="card-body text-center">
                                @php
                                    $latestImage = $camera->imageRecords->first();
                                @endphp
                                <img class="img-fluid rounded camera-feed-image"
                                    style="height: 180px; width: 100%; object-fit: cover;"
                                    id="camera-feed-{{ $camera->id }}"
                                    data-camera-id="{{ $camera->id }}"
                                    src="{{ $latestImage ? \Illuminate\Support\Facades\Storage::url($latestImage->path) : 'https://placehold.co/600x400/293445/FFFFFF?text=No+Image' }}"
                                    alt="Live feed untuk {{ $camera->name }}">
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small class="text-muted camera-timestamp">
                                    Update: {{ $latestImage ? $latestImage->captured_at->format('H:i:s') . ' WIB' : 'N/A' }}
                                </small>
                                <a href="{{ route('log.history.explorer', $camera->id) }}"
                                    class="btn btn-sm btn-outline-primary">Riwayat</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-camera-off" style="font-size: 48px; color: #ccc;"></i>
                    <h5 class="card-title mt-3">Belum Ada Kamera</h5>
                    <p class="card-text">
                        @if($currentGroup != 'Semua Kamera')
                            Tidak ada kamera di grup "{{ $currentGroup }}".
                        @else
                            Silakan daftarkan perangkat kamera baru untuk memulai pemantauan.
                        @endif
                    </p>
                    @role('admin')
                        <a href="{{ route('admin.cameras.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Tambah Kamera
                        </a>
                    @endrole
                </div>
            </div>
        </div>
    @endif
@endsection
