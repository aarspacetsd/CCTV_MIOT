@extends('layouts/layoutMaster')

@section('title', 'Log Deteksi ML')

@section('vendor-style')

<style>
.detection-thumbnail {
cursor: pointer;
transition: transform 0.2s ease;
border-radius: 6px;
box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.detection-thumbnail:hover {
transform: scale(1.05);
}
.confidence-badge {
font-weight: 600;
padding: 0.4em 0.8em;
}
.filter-section {
background-color: #f8f9fa;
border-bottom: 1px solid #e9ecef;
}
.stat-widget {
border-left: 4px solid #7367f0;
}
/* Merapikan tampilan pagination agar tidak berantakan */
.pagination-wrapper nav {
display: flex;
justify-content: space-between;
align-items: center;
width: 100%;
}
.pagination-wrapper .pagination {
margin-bottom: 0;
}
</style>

@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h4 class="mb-1">Log Deteksi Machine Learning</h4>
<p class="text-muted">Riwayat analisis objek cerdas dari seluruh kamera Anda.</p>
</div>
<div class="d-flex gap-2">
<button class="btn btn-label-secondary" onclick="location.reload()">
<i class="ti ti-refresh me-1"></i> Muat Ulang
</button>
<button class="btn btn-primary">
<i class="ti ti-download me-1"></i> Ekspor CSV
</button>
</div>
</div>

{{-- Ringkasan Statistik --}}

<div class="row mb-4 g-4">
<div class="col-md-3">
<div class="card stat-widget h-100 shadow-sm">
<div class="card-body">
<small class="text-muted d-block mb-1">Total Deteksi (24j)</small>
<h3 class="mb-0">{{ number_format($stats['total_24h'] ?? 0) }}</h3>
</div>
</div>
</div>
<div class="col-md-3">
<div class="card h-100 shadow-sm" style="border-left: 4px solid #28c76f;">
<div class="card-body">
<small class="text-muted d-block mb-1">Akurasi Rata-rata</small>
<h3 class="mb-0">{{ $stats['avg_accuracy'] ?? '0' }}%</h3>
</div>
</div>
</div>
<div class="col-md-3">
<div class="card h-100 shadow-sm" style="border-left: 4px solid #ff9f43;">
<div class="card-body">
<small class="text-muted d-block mb-1">Objek Terbanyak</small>
<h3 class="mb-0">{{ $stats['top_object'] ?? '-' }}</h3>
</div>
</div>
</div>
<div class="col-md-3">
<div class="card h-100 shadow-sm" style="border-left: 4px solid #ea5455;">
<div class="card-body">
<small class="text-muted d-block mb-1">Peringatan Tinggi</small>
<h3 class="mb-0">{{ $stats['high_alerts'] ?? 0 }}</h3>
</div>
</div>
</div>
</div>

<div class="card shadow-sm border-0">
{{-- Toolbar & Filter --}}
<div class="card-header filter-section py-3">
<form action="{{ request()->url() }}" method="GET">
<div class="row g-3">
<div class="col-md-4">
<div class="input-group input-group-merge">
<span class="input-group-text"><i class="ti ti-search"></i></span>
<input type="text" name="search" class="form-control" placeholder="Cari objek..." value="{{ request('search') }}">
</div>
</div>
<div class="col-md-3">
<select name="camera_id" class="form-select">
<option value="">Semua Kamera Anda</option>
@foreach(auth()->user()->cameras as $userCamera)
<option value="{{ $userCamera->id }}" {{ request('camera_id') == $userCamera->id ? 'selected' : '' }}>
{{ $userCamera->name }}
</option>
@endforeach
</select>
</div>
<div class="col-md-3">
<select name="type" class="form-select">
<option value="">Tipe Objek (Semua)</option>
<option value="person" {{ request('type') == 'person' ? 'selected' : '' }}>Orang</option>
<option value="vehicle" {{ request('type') == 'vehicle' ? 'selected' : '' }}>Kendaraan</option>
<option value="animal" {{ request('type') == 'animal' ? 'selected' : '' }}>Hewan</option>
</select>
</div>
<div class="col-md-2">
<button type="submit" class="btn btn-secondary w-100"><i class="ti ti-filter"></i> Filter</button>
</div>
</div>
</form>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 120px;">Pratinjau</th>
                <th>Kamera & Lokasi</th>
                <th>Hasil Deteksi</th>
                <th>Waktu Kejadian</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detectionLogs as $log)
                {{-- Hanya tampilkan jika kamera milik user login --}}
                @if($log->camera && $log->camera->user_id === auth()->id())
                    <tr>
                        <td>
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($log->path) }}"
                                 alt="Pratinjau Deteksi"
                                 class="detection-thumbnail img-fluid"
                                 data-bs-toggle="modal"
                                 data-bs-target="#previewModal{{ $log->id }}">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $log->camera->name ?? 'Kamera Tidak Diketahui' }}</span>
                                <small class="text-muted">
                                    <i class="ti ti-map-pin ti-xs"></i> {{ $log->camera->group->name ?? 'Tanpa Grup' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <span class="badge bg-label-primary confidence-badge">
                                    <i class="ti ti-scan ti-xs me-1"></i> {{ $log->object_type ?? 'Objek' }}
                                </span>

                                @php
                                    $confidence = $log->confidence ?? 0;
                                    $color = $confidence >= 80 ? 'success' : ($confidence >= 50 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $color }} confidence-badge">
                                    {{ $confidence }}% Akurasi
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $log->captured_at->translatedFormat('d M Y') }}</span>
                                <small class="text-muted">{{ $log->captured_at->format('H:i:s') }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('log.history.explorer', ['camera' => $log->camera_id, 'date' => $log->captured_at->format('Y-m-d')]) }}"
                                    class="btn btn-sm btn-icon btn-label-secondary" title="Lihat di Explorer">
                                    <i class="ti ti-external-link"></i>
                                </a>
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($log->path) }}" download
                                   class="btn btn-sm btn-icon btn-label-primary" title="Unduh Gambar">
                                    <i class="ti ti-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Preview Per Baris --}}
                    <div class="modal fade" id="previewModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title">Detail Deteksi: {{ $log->camera->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($log->path) }}" class="img-fluid rounded shadow w-100 mb-3" alt="Full Image">
                                    <div class="p-3 bg-light rounded d-flex justify-content-between text-start">
                                        <div>
                                            <p class="mb-1 small text-muted text-uppercase">Waktu</p>
                                            <p class="mb-0 fw-bold">{{ $log->captured_at->translatedFormat('d F Y, H:i:s') }}</p>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted text-uppercase">Objek & Akurasi</p>
                                            <span class="badge bg-primary text-capitalize">{{ $log->object_type ?? 'N/A' }} ({{ $log->confidence ?? 0 }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <i class="ti ti-search-off text-muted mb-2" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">Tidak ada deteksi ditemukan</h5>
                            <p class="text-muted mb-0">Pastikan kamera Anda aktif atau coba ubah filter pencarian.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($detectionLogs->hasPages())
    <div class="card-footer border-top pagination-wrapper">
        {{ $detectionLogs->links('pagination::bootstrap-5') }}
    </div>
@endif


</div>

@endsection
