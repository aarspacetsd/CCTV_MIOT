@extends('layouts/layoutMaster')

@section('title', 'Manajemen Grup Kamera')

@section('vendor-style')
<style>
.group-card {
    transition: all 0.3s ease;
    border-left: 4px solid #667eea;
}

.group-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.camera-badge {
    display: inline-flex;
    align-items: center;
    margin: 4px;
    padding: 6px 10px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    max-width: 100%; /* Mencegah badge keluar container */
}

.camera-badge span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px; /* Batas panjang nama di badge */
}

.camera-badge:hover {
    background: #e9ecef;
}

.ungrouped-area {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    border-radius: 8px;
    padding: 20px;
    min-height: 150px;
}

.group-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 8px 8px 0 0;
    /* Flex adjustment handled in inline classes for responsiveness */
}

/* Helper untuk text truncate yang lebih baik di dalam flex */
.min-w-0 {
    min-width: 0;
}
</style>
@endsection

@section('content')
{{-- BLOK PESAN SESSION --}}
@if(session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="ti ti-info-circle me-2"></i>{{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Header Halaman: Responsif flex-column di mobile --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="mb-1">
            <i class="ti ti-folders me-2"></i>Manajemen Grup Kamera
        </h4>
        <p class="mb-0 text-muted">Kelola dan atur kamera ke dalam grup untuk kemudahan monitoring</p>
    </div>
    <div class="align-self-end align-self-md-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
            <i class="ti ti-plus me-1"></i> <span class="d-none d-sm-inline">Buat Grup Baru</span><span class="d-inline d-sm-none">Buat</span>
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
{{-- END BLOK PESAN SESSION --}}

{{-- Kamera yang Belum Dikelompokkan --}}
@if($ungroupedCameras->count() > 0)
<div class="card mb-4">
    <div class="card-body ungrouped-area">
        <h5 class="mb-3">
            <i class="ti ti-alert-circle me-2"></i>
            Kamera Tanpa Grup ({{ $ungroupedCameras->count() }})
        </h5>
        <p class="text-muted mb-3">Kamera di bawah ini belum dimasukkan ke dalam grup manapun</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($ungroupedCameras as $camera)
            <div class="camera-badge" title="{{ $camera->name }}">
                <i class="ti ti-camera me-2 flex-shrink-0"></i>
                <span>{{ $camera->name }}</span>
                <button type="button"
                        class="btn btn-sm btn-primary ms-2 flex-shrink-0"
                        style="padding: 0.1rem 0.4rem;"
                        data-bs-toggle="modal"
                        data-bs-target="#assignCameraModal"
                        data-camera-id="{{ $camera->id }}"
                        data-camera-name="{{ $camera->name }}">
                    <i class="ti ti-arrow-right" style="font-size: 0.8rem;"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Daftar Grup --}}
<div class="row g-4"> {{-- Tambahkan g-4 untuk spacing antar grid --}}
    {{-- Grup Kosong (jika ada) --}}
    @if(!empty($emptyGroups))
        @foreach($emptyGroups as $emptyGroupName)
        <div class="col-md-6 col-xl-4">
            <div class="card group-card h-100 border-warning">
                <div class="group-header-custom d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);">
                    <div class="min-w-0 me-2">
                        <h5 class="mb-0 text-white text-truncate" title="{{ $emptyGroupName }}">
                            <i class="ti ti-folder me-2"></i>{{ $emptyGroupName }}
                        </h5>
                        <small class="text-white-50">0 Kamera</small>
                    </div>
                    <div class="dropdown flex-shrink-0">
                        <button class="btn btn-sm btn-light bg-white bg-opacity-25 border-0 text-white" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#"
                                   data-bs-toggle="modal"
                                   data-bs-target="#editGroupModal"
                                   data-group-name="{{ $emptyGroupName }}">
                                    <i class="ti ti-edit me-2"></i>Edit Nama
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                   onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus grup kosong ini?')) document.getElementById('delete-empty-group-{{ \Illuminate\Support\Str::slug($emptyGroupName) }}').submit();">
                                    <i class="ti ti-trash me-2"></i>Hapus Grup
                                </a>
                                <form id="delete-empty-group-{{ \Illuminate\Support\Str::slug($emptyGroupName) }}"
                                      action="{{ route('admin.camera-groups.destroy', $emptyGroupName) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0 d-flex align-items-center">
                        <i class="ti ti-alert-circle me-2 fs-4"></i>
                        <div class="small">Kosong. Tambahkan kamera dari area atas.</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    {{-- Grup yang Sudah Ada Kameranya --}}
    @forelse($groupedCameras as $groupName => $cameras)
    <div class="col-md-6 col-xl-4">
        <div class="card group-card h-100">
            <div class="group-header-custom d-flex justify-content-between align-items-center">
                <div class="min-w-0 me-2">
                    <h5 class="mb-0 text-white text-truncate" title="{{ $groupName }}">
                        <i class="ti ti-folder me-2"></i>{{ $groupName }}
                    </h5>
                    <small class="text-white-50">{{ $cameras->count() }} Kamera</small>
                </div>
                <div class="dropdown flex-shrink-0">
                    <button class="btn btn-sm btn-light bg-white bg-opacity-25 border-0 text-white" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#"
                               data-bs-toggle="modal"
                               data-bs-target="#editGroupModal"
                               data-group-name="{{ $groupName }}">
                                <i class="ti ti-edit me-2"></i>Edit Nama
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                               onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus grup ini? Semua kamera akan menjadi ungrouped.')) document.getElementById('delete-group-{{ \Illuminate\Support\Str::slug($groupName) }}').submit();">
                                <i class="ti ti-trash me-2"></i>Hapus Grup
                            </a>
                            <form id="delete-group-{{ \Illuminate\Support\Str::slug($groupName) }}"
                                  action="{{ route('admin.camera-groups.destroy', $groupName) }}"
                                  method="POST"
                                  class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    @foreach($cameras as $camera)
                    {{-- Item Kamera: Menggunakan flex-wrap agar aman di mobile --}}
                    <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between align-items-center p-2 border rounded gap-2">
                        {{-- Nama & Icon: Truncate jika panjang --}}
                        <div class="d-flex align-items-center gap-2 min-w-0" style="flex: 1;">
                            <i class="ti ti-camera text-primary flex-shrink-0"></i>
                            <span class="text-truncate fw-medium" title="{{ $camera->name }}">
                                {{ $camera->name }}
                            </span>
                        </div>

                        {{-- Badge & Tombol: Tidak boleh shrink --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-auto ms-sm-0">
                            <span class="badge {{ $camera->is_active ? 'bg-label-success' : 'bg-label-secondary' }} badge-sm">
                                {{ $camera->is_active ? 'Aktif' : 'Offline' }}
                            </span>
                            <button type="button"
                                    class="btn btn-icon btn-sm btn-outline-danger"
                                    onclick="event.preventDefault(); if(confirm('Hapus {{ $camera->name }} dari grup ini?')) document.getElementById('remove-camera-{{ $camera->id }}').submit();">
                                <i class="ti ti-x"></i>
                            </button>
                            <form id="remove-camera-{{ $camera->id }}"
                                  action="{{ route('admin.camera-groups.remove') }}"
                                  method="POST"
                                  class="d-none">
                                @csrf
                                <input type="hidden" name="camera_id" value="{{ $camera->id }}">
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @empty
        @if(empty($emptyGroups))
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                         <div class="badge bg-label-primary rounded p-3">
                            <i class="ti ti-folder-off fs-1"></i>
                         </div>
                    </div>
                    <h5 class="mb-1">Belum Ada Grup</h5>
                    <p class="text-muted mb-4">Buat grup baru untuk mulai mengorganisir kamera Anda</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                        <i class="ti ti-plus me-1"></i> Buat Grup Pertama
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endforelse
</div>

{{-- Modal: Buat Grup Baru --}}
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.camera-groups.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-folder-plus me-2"></i>Buat Grup Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="group_name" class="form-label">Nama Grup <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="group_name"
                               name="group_name"
                               placeholder="Contoh: Lantai 1, Area Parkir, dll"
                               required>
                    </div>

                    @if($ungroupedCameras->count() > 0)
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Tambahkan Kamera (Opsional)</span>
                            <span class="badge bg-label-primary">{{ $ungroupedCameras->count() }} Tersedia</span>
                        </label>
                        <div class="border rounded p-0" style="max-height: 300px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                @foreach($ungroupedCameras as $camera)
                                <label class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer">
                                    <input class="form-check-input me-3"
                                           type="checkbox"
                                           name="camera_ids[]"
                                           value="{{ $camera->id }}">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $camera->name }}</span>
                                        <small class="text-muted">
                                            Status: <span class="{{ $camera->is_active ? 'text-success' : 'text-secondary' }}">{{ $camera->is_active ? 'Online' : 'Offline' }}</span>
                                        </small>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning d-flex align-items-center mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <div>Semua kamera sudah memiliki grup. Grup akan dibuat kosong.</div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Nama Grup --}}
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="editGroupForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-edit me-2"></i>Edit Nama Grup
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="old_group_name" class="form-label">Nama Grup Saat Ini</label>
                        <input type="text"
                               class="form-control"
                               id="old_group_name"
                               readonly
                               disabled
                               style="background-color: #f5f5f9;">
                    </div>
                    <div class="mb-3">
                        <label for="new_group_name" class="form-label">Nama Grup Baru</label>
                        <input type="text"
                               class="form-control"
                               id="new_group_name"
                               name="new_group_name"
                               placeholder="Masukkan nama baru"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Assign Kamera ke Grup --}}
<div class="modal fade" id="assignCameraModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.camera-groups.assign') }}" method="POST">
                @csrf
                <input type="hidden" name="camera_id" id="assign_camera_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-link me-2"></i>Pindahkan Kamera
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assign_camera_name" class="form-label">Kamera Terpilih</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-camera"></i></span>
                            <input type="text"
                                   class="form-control"
                                   id="assign_camera_name"
                                   readonly
                                   disabled
                                   style="background-color: #f5f5f9;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assign_group_name" class="form-label">Pilih Grup Tujuan</label>
                        <select class="form-select" name="group_name" id="assign_group_name" required>
                            <option value="">-- Pilih Grup --</option>
                            @foreach($groups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                            @if(!empty($emptyGroups))
                            <optgroup label="Grup Kosong">
                                @foreach($emptyGroups as $emptyGroup)
                                <option value="{{ $emptyGroup }}">{{ $emptyGroup }} (Kosong)</option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        <div class="form-text mt-2">
                            Grup tidak ada? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#createGroupModal">Buat baru</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
// Handle Edit Group Modal
document.getElementById('editGroupModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const groupName = button.getAttribute('data-group-name');

    document.getElementById('old_group_name').value = groupName;
    document.getElementById('new_group_name').value = groupName;

    const form = document.getElementById('editGroupForm');

    // Generate URL dengan placeholder yang aman untuk menghindari ParseError PHP
    let updateUrl = "{{ route('admin.camera-groups.update', 'PLACEHOLDER_NAME') }}";

    // Ganti placeholder dengan value sebenarnya menggunakan JavaScript
    form.action = updateUrl.replace('PLACEHOLDER_NAME', groupName);
});

// Handle Assign Camera Modal
document.getElementById('assignCameraModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const cameraId = button.getAttribute('data-camera-id');
    const cameraName = button.getAttribute('data-camera-name');

    document.getElementById('assign_camera_id').value = cameraId;
    document.getElementById('assign_camera_name').value = cameraName;
});
</script>
@endsection
