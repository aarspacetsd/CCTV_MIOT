@extends('layouts/layoutMaster')

@section('title', 'Log Deteksi ML')

@section('content')
    <h4 class="mb-4">Log Deteksi Machine Learning</h4>

    <div class="card">
        <h5 class="card-header">Deteksi Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pratinjau</th>
                        <th>Kamera</th>
                        <th>Deteksi</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detectionLogs as $log)
                        <tr>
                            <td>
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($log->path) }}" alt="Pratinjau Deteksi"
                                    class="img-thumbnail" style="width: 100px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $log->camera->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                {{-- Contoh data statis, ganti dengan data asli dari $log->detection_result nantinya --}}
                                <span class="badge bg-label-info">Orang</span>
                                <span class="badge bg-label-success">89%</span>
                            </td>
                            <td>
                                {{ $log->captured_at->format('d M Y, H:i:s') }}
                            </td>
                            <td>
                                {{-- Tautan ke halaman detail riwayat untuk melihat gambar ukuran penuh --}}
                                <a href="{{ route('log.history.images', ['camera' => $log->camera_id, 'date' => $log->captured_at->format('Y-m-d')]) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada deteksi yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($detectionLogs->hasPages())
            <div class="card-footer">
                {{ $detectionLogs->links() }}
            </div>
        @endif
    </div>
@endsection
