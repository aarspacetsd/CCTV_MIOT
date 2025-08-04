@extends('layouts/layoutMaster')

@section('title', 'Detail Rekaman - ' . \Carbon\Carbon::parse($formattedDate)->format('j M Y'))

@section('page-style')
    <style>
        .image-list-container {
            max-height: 60vh;
            overflow-y: auto;
        }

        .list-group-item.active {
            background-color: #e7e7ff;
            border-color: #e7e7ff;
            color: #7367f0;
        }

        #image-viewer-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 400px;
            background-color: #f8f7fa;
            border-radius: 0.375rem;
            border: 2px dashed #d9d7e8;
            color: #a8a4c1;
        }

        #image-viewer-display {
            display: none;
        }
    </style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageListViewer = document.getElementById('image-viewer-display-img');
            const imageViewerTimestamp = document.getElementById('image-viewer-timestamp');
            const placeholder = document.getElementById('image-viewer-placeholder');
            const imageViewerDisplay = document.getElementById('image-viewer-display');
            const listItems = document.querySelectorAll('.image-list-item');

            listItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    listItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    const imageUrl = this.getAttribute('data-img-src');
                    const timestamp = this.getAttribute('data-timestamp');
                    placeholder.style.display = 'none';
                    imageViewerDisplay.style.display = 'block';
                    imageListViewer.src = imageUrl;
                    imageViewerTimestamp.textContent = timestamp;
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="mb-0">Detail Rekaman: {{ \Carbon\Carbon::parse($formattedDate)->translatedFormat('l, j F Y') }}</h4>
            <p class="text-muted mb-0">Kamera: {{ $camera->name }}</p>
        </div>
        <a href="{{ route('log.history.folders', $camera->id) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            <span class="align-middle">Kembali ke Daftar Tanggal</span>
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-xl-3">
            <div class="card">
                <h5 class="card-header">Daftar Rekaman</h5>
                <div class="list-group list-group-flush image-list-container">
                    @forelse($images as $image)
                        {{-- FIX: Menggunakan namespace lengkap untuk fasad Storage --}}
                        <a href="#" class="list-group-item list-group-item-action image-list-item"
                            data-img-src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}"
                            data-timestamp="{{ $image->captured_at->format('H:i:s') }} WIB">
                            <i class="ti ti-photo me-2"></i> Rekaman Pukul {{ $image->captured_at->format('H:i:s') }}
                        </a>
                    @empty
                        <div class="list-group-item text-center">
                            <p class="text-muted my-3">Tidak ada gambar pada tanggal ini.</p>
                        </div>
                    @endforelse
                </div>
                @if ($images->hasPages())
                    <div class="card-footer">
                        {{ $images->links() }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-8 col-xl-9">
            <div class="card">
                <div class="card-body" style="min-height: 500px;">
                    <div id="image-viewer-placeholder">
                        <div class="text-center">
                            <i class="ti ti-photo-search ti-lg mb-2"></i>
                            <p>Pilih item dari daftar di samping untuk melihat gambar.</p>
                        </div>
                    </div>
                    <div id="image-viewer-display">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Pratinjau Rekaman</h5>
                            <small class="text-muted" id="image-viewer-timestamp"></small>
                        </div>
                        <img id="image-viewer-display-img" src="" class="img-fluid rounded w-100"
                            alt="Pratinjau Gambar">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
