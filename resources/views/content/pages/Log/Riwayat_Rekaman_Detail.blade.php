@extends('layouts/layoutMaster')

@section('title', 'Detail Rekaman - ' . \Carbon\Carbon::parse($formattedDate)->format('j M Y'))

@section('page-style')
    <style>
        /* Layout flexbox untuk card yang sama tinggi */
        .image-list-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .image-list-container {
            flex-grow: 1;
            overflow-y: auto;
            max-height: 400px;
            /* Batasi tinggi untuk mencegah overflow */
        }

        .list-group-item.active {
            background-color: #e7e7ff;
            border-color: #e7e7ff;
            color: #7367f0;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
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

        /* Fix untuk pagination */
        .pagination {
            margin-bottom: 0;
            justify-content: center;
        }

        .pagination .page-link {
            color: #7367f0;
        }

        .pagination .page-item.active .page-link {
            background-color: #7367f0;
            border-color: #7367f0;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .image-list-container {
                max-height: 300px;
            }

            #image-viewer-placeholder {
                min-height: 300px;
            }
        }

        /* Fix untuk image viewer */
        #image-viewer-display-img {
            max-height: 500px;
            object-fit: contain;
            background-color: #f8f9fa;
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

            // Function untuk menampilkan gambar
            function showImage(item) {
                // Remove active class dari semua item
                listItems.forEach(i => i.classList.remove('active'));

                // Add active class ke item yang diklik
                item.classList.add('active');

                // Get data dari item
                const imageUrl = item.getAttribute('data-img-src');
                const timestamp = item.getAttribute('data-timestamp');

                // Hide placeholder dan show image viewer
                placeholder.style.display = 'none';
                imageViewerDisplay.style.display = 'block';

                // Set image source dan timestamp
                imageListViewer.src = imageUrl;
                imageViewerTimestamp.textContent = timestamp;

                // Handle image load error
                imageListViewer.onerror = function() {
                    this.src = '/images/placeholder.png'; // Fallback image
                    console.error('Failed to load image:', imageUrl);
                };
            }

            // Add click event listener ke setiap item
            listItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    showImage(this);
                });
            });

            // Auto-select first item jika ada
            if (listItems.length > 0) {
                // Uncomment baris berikut jika ingin auto-select item pertama
                // showImage(listItems[0]);
            }
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
        <!-- Left Column - Image List -->
        <div class="col-lg-4 col-xl-3">
            <div class="card image-list-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Rekaman</h5>
                    <small class="text-muted">{{ $images->total() }} item</small>
                </div>
                <div class="image-list-container">
                    <div class="list-group list-group-flush">
                        @forelse($images as $image)
                            <a href="#"
                                class="list-group-item list-group-item-action image-list-item d-flex align-items-center"
                                data-img-src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}"
                                data-timestamp="{{ $image->captured_at->format('H:i:s') }} WIB">
                                <i class="ti ti-photo me-2 text-primary"></i>
                                <div>
                                    <div class="fw-medium">{{ $image->captured_at->format('H:i:s') }}</div>
                                    <small class="text-muted">{{ $image->captured_at->format('d/m/Y') }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="ti ti-camera-off ti-lg text-muted mb-2"></i>
                                <p class="text-muted mb-0">Tidak ada gambar pada tanggal ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if ($images->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            {{ $images->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Image Viewer -->
        <div class="col-lg-8 col-xl-9">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <!-- Placeholder -->
                    <div id="image-viewer-placeholder">
                        <div class="text-center">
                            <i class="ti ti-photo-search" style="font-size: 3rem;" class="mb-3 text-muted"></i>
                            <h5 class="text-muted">Pilih Rekaman</h5>
                            <p class="text-muted mb-0">Pilih item dari daftar di samping untuk melihat gambar.</p>
                        </div>
                    </div>

                    <!-- Image Display -->
                    <div id="image-viewer-display" class="flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Pratinjau Rekaman</h5>
                            <small class="text-muted" id="image-viewer-timestamp"></small>
                        </div>
                        <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                            <img id="image-viewer-display-img" src="" class="img-fluid rounded shadow-sm"
                                alt="Pratinjau Gambar" style="max-width: 100%; max-height: 100%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
