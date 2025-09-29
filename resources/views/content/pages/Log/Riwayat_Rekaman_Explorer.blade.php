@extends('layouts/layoutMaster')

@section('title', 'Riwayat Explorer - ' . $camera->name)

{{-- Tambahkan CSS untuk FancyBox dan Flatpickr --}}
@section('vendor-style')
    @vite(['node_modules/@fancyapps/ui/dist/fancybox/fancybox.css', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('page-style')
    {{-- CSS Kustom untuk tampilan folder dan galeri --}}
    <style>
        .folder-item,
        .gallery-item {
            border: 1px solid #dbdade;
            border-radius: 0.375rem;
            padding: 1.25rem;
            transition: all 0.2s ease-in-out;
            background-color: #fff;
        }

        .folder-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-color: var(--bs-primary);
        }

        .folder-icon {
            font-size: 2.5rem;
            color: #b9b8c3;
        }

        .folder-item:hover .folder-icon {
            color: var(--bs-primary);
        }

        .gallery-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 0.25rem;
            cursor: pointer;
        }

        .breadcrumb-item a {
            color: var(--bs-primary);
        }

        .breadcrumb-item.active {
            color: #6f6b7d;
        }

        .delete-form {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
@endsection

@section('content')
    {{-- Header Halaman dan Breadcrumbs --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('log.history.index') }}"><i class="ti ti-home"></i></a>
                    </li>
                    @foreach ($breadcrumbs as $index => $breadcrumb)
                        @if ($breadcrumb['url'])
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            <p class="text-muted mb-0 mt-1">Jelajahi rekaman yang tersimpan untuk kamera {{ $camera->name }}.</p>
        </div>
        <a href="{{ route('log.history.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            <span class="align-middle">Kembali ke Pilih Kamera</span>
        </a>
    </div>

    {{-- KARTU FILTER CEPAT --}}
    @if ($level !== 'date')
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Navigasi Cepat</h5>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="filter-date" class="form-label">Tanggal</label>
                        <input type="text" id="filter-date" class="form-control" placeholder="YYYY-MM-DD"
                            value="{{ $filter['date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-hour" class="form-label">Jam</label>
                        <select id="filter-hour" class="form-select">
                            <option value="">-- Pilih Jam --</option>
                            {{-- BARU: Logika untuk menandai jam yang tersedia --}}
                            @for ($h = 0; $h < 24; $h++)
                                @php $hourKey = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $hourKey }}"
                                    {{ !isset($availableTimes[$hourKey]) ? 'disabled' : '' }}>
                                    {{ $hourKey }}:00 {{ isset($availableTimes[$hourKey]) ? '✔' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-minute" class="form-label">Menit</label>
                        <select id="filter-minute" class="form-select">
                            <option value="">-- Pilih Menit --</option>
                            {{-- BARU: Dibuat disable, akan diaktifkan oleh JS --}}
                            @for ($m = 0; $m < 60; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" disabled>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="filter-go-btn" class="btn btn-primary w-100">
                            <i class="ti ti-player-play me-1"></i>Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tampilan Konten Dinamis --}}
    <div class="card">
        <div class="card-body">
            @if (count($items) > 0)
                <h5 class="card-title mb-4">
                    @if ($level === 'date')
                        Pilih Tanggal Rekaman
                    @elseif ($level === 'hour')
                        Pilih Jam Rekaman
                    @elseif ($level === 'minute')
                        Pilih Menit Rekaman
                    @elseif ($level === 'chunk')
                        Pilih Grup Rekaman
                    @else
                        Galeri Gambar ({{ count($items) }} gambar)
                    @endif
                </h5>
                <div class="row g-4">
                    @if ($level === 'gallery')
                        @foreach ($items as $image)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="gallery-item text-center">
                                    <a href="{{ $image['url'] }}" data-fancybox="gallery"
                                        data-caption="Waktu Rekaman: {{ $image['time'] }}">
                                        <img src="{{ $image['url'] }}" alt="Rekaman {{ $image['name'] }}">
                                    </a>
                                    <small class="d-block mt-2 text-muted">{{ $image['time'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($items as $item)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ $item['url'] }}" class="text-decoration-none">
                                    <div class="folder-item d-flex align-items-center gap-3 position-relative">
                                        <i class="ti ti-folder folder-icon"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $item['name'] }}</h6>
                                            <small class="text-muted">{{ $item['count'] }} rekaman</small>
                                        </div>
                                        @if ($level === 'date')
                                            <form action="{{ route('log.history.destroy.folder', $camera->id) }}"
                                                method="POST" class="delete-form"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua {{ $item['count'] }} rekaman untuk tanggal {{ $item['name'] }}? Tindakan ini tidak dapat diurungkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="date" value="{{ $item['raw_date'] }}">
                                                <button type="submit"
                                                    class="btn btn-sm btn-icon btn-text-danger rounded-pill">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                @if ($level === 'date' && $items->hasPages())
                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="ti ti-photo-off ti-lg text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada rekaman yang ditemukan di sini.</p>
                    @if (count($breadcrumbs) > 1)
                        <a href="{{ $breadcrumbs[count($breadcrumbs) - 2]['url'] }}" class="btn btn-primary mt-2">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@section('vendor-script')
    @vite(['resources/assets/js/vendors/fancybox.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
    <script>
        // BARU: Kirim data dari PHP ke JavaScript
        const availableTimes = @json($availableTimes ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            const filterDateInput = document.getElementById('filter-date');
            if (filterDateInput) {
                flatpickr(filterDateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd F Y',
                });

                const hourSelect = document.getElementById('filter-hour');
                const minuteSelect = document.getElementById('filter-minute');
                const currentHour = "{{ $filter['hour'] ?? '' }}";
                const currentMinute = "{{ $filter['minute'] ?? '' }}";

                // BARU: Fungsi untuk memperbarui opsi menit
                function updateMinuteOptions() {
                    const selectedHour = hourSelect.value;
                    const availableMinutesForHour = availableTimes[selectedHour] || [];

                    if (!availableMinutesForHour.includes(minuteSelect.value)) {
                        minuteSelect.value = '';
                    }

                    for (const option of minuteSelect.options) {
                        if (option.value === '') continue;
                        if (availableMinutesForHour.includes(option.value)) {
                            option.disabled = false;
                            option.textContent = `${option.value} ✔`;
                        } else {
                            option.disabled = true;
                            option.textContent = option.value;
                        }
                    }
                }

                if (currentHour) {
                    hourSelect.value = currentHour;
                    updateMinuteOptions(); // Panggil fungsi saat halaman dimuat
                    if (currentMinute) {
                        minuteSelect.value = currentMinute;
                    }
                }

                // BARU: Tambahkan event listener untuk perubahan jam
                hourSelect.addEventListener('change', updateMinuteOptions);

                document.getElementById('filter-go-btn').addEventListener('click', function() {
                    const date = filterDateInput.value;
                    const hour = hourSelect.value;
                    const minute = minuteSelect.value;
                    if (!date) {
                        alert('Silakan pilih tanggal terlebih dahulu.');
                        return;
                    }
                    let baseUrl = "{{ route('log.history.explorer', $camera->id) }}";
                    let finalUrl = `${baseUrl}/${date}`;
                    if (hour) {
                        finalUrl += `/${hour}`;
                    }
                    if (hour && minute) {
                        finalUrl += `/${minute}`;
                    }
                    window.location.href = finalUrl;
                });
            }
        });
    </script>
    @endsections
