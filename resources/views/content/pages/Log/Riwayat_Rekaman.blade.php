@extends('layouts/layoutMaster')

@section('title', 'Riwayat Kamera - ' . $camera->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="mb-0">Riwayat Kamera: {{ $camera->name }}</h4>
            <p class="text-muted mb-0">Pilih tanggal untuk melihat rekaman gambar.</p>
        </div>
        <a href="{{ route('cameras.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            <span class="align-middle">Kembali ke Manajemen Kamera</span>
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Arsip Rekaman Tersimpan</h5>
        <div class="list-group list-group-flush">
            @forelse($dates as $record)
                <a href="{{ route('log.history.images', ['camera' => $camera->id, 'date' => $record->date]) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-folder-filled ti-lg me-3 text-warning"></i>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($record->date)->translatedFormat('l, j F Y') }}</h6>
                        </div>
                    </div>
                    <span class="badge bg-label-primary rounded-pill">Lihat Rekaman <i
                            class="ti ti-chevron-right ms-1 ti-xs"></i></span>
                </a>
            @empty
                <div class="list-group-item text-center">
                    <p class="text-muted my-3">Belum ada rekaman yang tersimpan untuk kamera ini.</p>
                </div>
            @endforelse
        </div>
        @if ($dates->hasPages())
            <div class="card-footer">
                {{ $dates->links() }}
            </div>
        @endif
    </div>
@endsection
