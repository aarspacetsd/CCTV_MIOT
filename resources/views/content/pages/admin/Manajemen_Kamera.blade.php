@extends('layouts/layoutMaster')

@section('title', 'Manajemen Kamera')

@section('page-script')
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
@endsection

@section('content')

    {{-- TAMPILAN DAFTAR KAMERA (INDEX) --}}
    @if (isset($view) && $view == 'index')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Daftar Perangkat Kamera</h4>
            {{-- FIX: Menggunakan nama route yang benar --}}
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
                            <th>Tanggal Dibuat</th>
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
                                    @if ($camera->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $camera->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- FIX: Menggunakan nama route yang benar --}}
                                        <a href="{{ route('admin.cameras.edit', $camera->id) }}" class="text-body"><i
                                                class="ti ti-edit ti-sm me-2"></i></a>
                                        {{-- FIX: Menggunakan nama route yang benar --}}
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
        <h4 class="mb-4">Registrasi Perangkat Kamera Baru</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Langkah 1: Masukkan Detail Kamera</h5>
                    <div class="card-body">
                        {{-- FIX: Menggunakan nama route yang benar --}}
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
                            {{-- FIX: Menggunakan nama route yang benar
                            <a href="{{ route('admin.cameras.index') }}" class="btn btn-secondary">Batal</a> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- TAMPILAN FORM EDIT KAMERA (EDIT) --}}
    @if (isset($view) && $view == 'edit')
        <h4 class="mb-4">Edit Detail Kamera</h4>
        @if (session('newCamera'))
            <div class="card mb-4">
                <h5 class="card-header text-success"><i class="ti ti-circle-check-filled me-2"></i>Langkah 2: Salin
                    Informasi ke Perangkat</h5>
                <div class="card-body">
                    <p>Pendaftaran berhasil! Gunakan informasi di bawah ini untuk mengkonfigurasi perangkat ESP32-CAM Anda.
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading mb-1"><i class="ti ti-alert-triangle-filled me-1"></i>Penting!</h6>
                        <span>Simpan semua kunci ini di tempat yang aman. Kunci ini tidak akan ditampilkan lagi setelah Anda
                            meninggalkan halaman ini.</span>
                    </div>
                    <div class="mb-3">
                        <label for="device_id_input" class="form-label">Device ID (Untuk Upload Gambar)</label>
                        <div class="input-group">
                            <input type="text" readonly class="form-control" id="device_id_input"
                                value="{{ session('newCamera')->device_id }}">
                            <button class="btn btn-outline-secondary" type="button" id="copyDeviceBtn"><i
                                    class="ti ti-copy ti-xs me-1"></i> Salin</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="api_key_input" class="form-label">API Key (Untuk Upload Gambar)</label>
                        <div class="input-group">
                            <input type="text" readonly class="form-control" id="api_key_input"
                                value="{{ session('newCamera')->api_key }}">
                            <button class="btn btn-outline-secondary" type="button" id="copyApiBtn"><i
                                    class="ti ti-copy ti-xs me-1"></i> Salin</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="websocket_id_input" class="form-label">WebSocket Channel ID (Untuk Status
                            Real-time)</label>
                        <div class="input-group">
                            <input type="text" readonly class="form-control" id="websocket_id_input"
                                value="{{ session('newCamera')->websocket_channel_id }}">
                            <button class="btn btn-outline-secondary" type="button" id="copyWebsocketBtn"><i
                                    class="ti ti-copy ti-xs me-1"></i> Salin</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Detail Kamera</h5>
                    <div class="card-body">
                        {{-- FIX: Menggunakan nama route yang benar --}}
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
                                    <option value="0" {{ !$camera->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                            </button>
                            {{-- FIX: Menggunakan nama route yang benar --}}
                            <a href="{{ route('admin.cameras.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
