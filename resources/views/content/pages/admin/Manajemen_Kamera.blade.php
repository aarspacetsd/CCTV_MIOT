@extends('layouts/layoutMaster')

@section('title', 'Manajemen Kamera')

@section('page-script')
    {{-- Skrip untuk menyalin ke clipboard di halaman edit/create --}}
    @if (isset($view) && ($view == 'edit' || $view == 'create'))
        <script>
            function copyToClipboard(elementId, buttonElement) {
                const input = document.getElementById(elementId);
                input.select();
                input.setSelectionRange(0, 99999);
                try {
                    navigator.clipboard.writeText(input.value).then(() => {
                        const originalText = buttonElement.innerHTML;
                        buttonElement.innerHTML = '<i class="ti ti-check ti-xs me-1"></i> Disalin!';
                        setTimeout(() => {
                            buttonElement.innerHTML = originalText;
                        }, 2000);
                    });
                } catch (err) {
                    document.execCommand('copy');
                    const originalText = buttonElement.innerHTML;
                    buttonElement.innerHTML = '<i class="ti ti-check ti-xs me-1"></i> Disalin!';
                    setTimeout(() => {
                        buttonElement.innerHTML = originalText;
                    }, 2000);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const copyDeviceBtn = document.getElementById('copyDeviceBtn');
                const copyApiBtn = document.getElementById('copyApiBtn');
                const copyWebsocketBtn = document.getElementById('copyWebsocketBtn');

                if (copyDeviceBtn) {
                    copyDeviceBtn.addEventListener('click', () => copyToClipboard('device_id_input', copyDeviceBtn));
                }
                if (copyApiBtn) {
                    copyApiBtn.addEventListener('click', () => copyToClipboard('api_key_input', copyApiBtn));
                }
                if (copyWebsocketBtn) {
                    copyWebsocketBtn.addEventListener('click', () => copyToClipboard('websocket_id_input',
                        copyWebsocketBtn));
                }
            });
        </script>
    @endif

    {{-- [BARU] Skrip untuk auto-refresh status di halaman daftar kamera --}}
    @if (isset($view) && $view == 'index')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
                                        statusBadge.className = 'badge bg-label-success';
                                        statusBadge.textContent = 'Aktif';
                                    } else {
                                        statusBadge.className = 'badge bg-label-danger';
                                        statusBadge.textContent = 'Nonaktif';
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching camera statuses:', error);
                        });
                }

                checkCameraStatuses();
                setInterval(checkCameraStatuses, 10000); // Periksa status setiap 10 detik
            });
        </script>
    @endif
@endsection

@section('content')

    {{-- TAMPILAN DAFTAR KAMERA (INDEX) --}}
    @if (isset($view) && $view == 'index')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Daftar Perangkat Kamera</h4>
            <a href="{{ route('admin.cameras.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Kamera Baru
            </a>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th>Nama Kamera</th>
                            <th>Device ID</th>
                            <th>Status</th>
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cameras as $camera)
                            <tr>
                                <td><strong>{{ $camera->name }}</strong></td>
                                <td><span
                                        class="text-muted">{{ \Illuminate\Support\Str::limit($camera->device_id, 13) }}...</span>
                                </td>
                                <td>
                                    {{-- [PERBAIKAN] Menambahkan ID unik untuk setiap badge status --}}
                                    <span class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                                        id="camera-status-{{ $camera->id }}">
                                        {{ $camera->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.cameras.qrcode', $camera->id) }}" class="btn btn-sm btn-icon"
                                        title="Unduh QR Code">
                                        <i class="ti ti-qrcode"></i>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('admin.cameras.edit', $camera->id) }}" class="text-body"><i
                                                class="ti ti-edit ti-sm me-2"></i></a>
                                        <form action="{{ route('admin.cameras.destroy', $camera->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamera ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon text-body text-danger p-0"><i
                                                    class="ti ti-trash ti-sm"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kamera yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cameras->hasPages())
                <div class="card-footer">
                    {{ $cameras->links() }}
                </div>
            @endif
        </div>
    @endif


    {{-- TAMPILAN FORM TAMBAH KAMERA (CREATE) --}}
    @if (isset($view) && $view == 'create')
        {{-- Konten form create tidak berubah --}}
        <h4 class="mb-4">Registrasi Perangkat Kamera Baru</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Langkah 1: Masukkan Detail Kamera</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.cameras.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="camera_name" class="form-label">Nama Kamera <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="camera_name" name="name"
                                    value="{{ old('name') }}" placeholder="Contoh: Kamera Ruang Tamu" required>
                            </div>
                            <div class="mb-3">
                                <label for="camera_description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="camera_description" name="description" rows="3"
                                    placeholder="Contoh: Terpasang di sudut atas menghadap pintu masuk">{{ old('description') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-device-floppy me-1"></i> Daftarkan & Buat Kunci
                            </button>
                            <a href="{{ route('admin.cameras.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- TAMPILAN FORM EDIT KAMERA (EDIT) --}}
    @if (isset($view) && $view == 'edit')
        {{-- Konten form edit tidak berubah --}}
        <div class="row">
            <div class="col-md-8">
                <h4 class="mb-4">Edit Detail Kamera</h4>
                @if (session('newCamera'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <span class="alert-icon text-success me-2">
                            <i class="ti ti-circle-check-filled ti-md"></i>
                        </span>
                        <div>
                            <strong>Kamera berhasil didaftarkan!</strong> Salin informasi di bawah ini untuk perangkat
                            Anda.
                        </div>
                    </div>
                @endif
                <div class="card">
                    <h5 class="card-header">Detail Kamera</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.cameras.update', $camera->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="camera_name" class="form-label">Nama Kamera <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="camera_name" name="name"
                                    value="{{ old('name', $camera->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="camera_description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="camera_description" name="description" rows="3">{{ old('description', $camera->description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="is_active">
                                    <option value="1" {{ $camera->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$camera->is_active ? 'selected' : '' }}>Nonaktif
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.cameras.index') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <h4 class="mb-4">Informasi Perangkat</h4>
                <div class="card">
                    <h5 class="card-header">QR Code Perangkat</h5>
                    <div class="card-body text-center">
                        <p class="mb-2">Pindai kode ini untuk mendapatkan Device ID.</p>
                        <div class="p-2 border rounded d-inline-block bg-white">
                            {!! QrCode::size(180)->generate($camera->device_id) !!}
                        </div>
                        <a href="{{ route('admin.cameras.qrcode', $camera->id) }}" class="btn btn-primary mt-3 d-block">
                            <i class="ti ti-download me-1"></i> Unduh QR Code
                        </a>
                    </div>
                </div>

                <div class="card mt-4">
                    <h5 class="card-header">Kunci & ID</h5>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="device_id_input" class="form-label">Device ID</label>
                            <div class="input-group">
                                <input type="text" readonly class="form-control" id="device_id_input"
                                    value="{{ $camera->device_id }}">
                                <button class="btn btn-outline-secondary" type="button" id="copyDeviceBtn"><i
                                        class="ti ti-copy ti-xs me-1"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="api_key_input" class="form-label">API Key</label>
                            <div class="input-group">
                                <input type="text" readonly class="form-control" id="api_key_input"
                                    value="{{ $camera->api_key }}">
                                <button class="btn btn-outline-secondary" type="button" id="copyApiBtn"><i
                                        class="ti ti-copy ti-xs me-1"></i></button>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label for="websocket_id_input" class="form-label">WebSocket Channel ID</label>
                            <div class="input-group">
                                <input type="text" readonly class="form-control" id="websocket_id_input"
                                    value="{{ $camera->websocket_channel_id }}">
                                <button class="btn btn-outline-secondary" type="button" id="copyWebsocketBtn"><i
                                        class="ti ti-copy ti-xs me-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
