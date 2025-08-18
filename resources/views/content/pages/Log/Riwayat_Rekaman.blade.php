@extends('layouts/layoutMaster')

@section('title', 'Riwayat Kamera - ' . $camera->name)

{{-- Vendor Styles --}}
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss'])
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

{{-- Page Scripts --}}
@section('page-script')
    <script>
        window.addEventListener('load', function() {
            $(function() {
                const dataUrl = "{{ route('log.history.folders.data', ['camera' => $camera->id]) }}";

                var dt_basic = $('.datatables-basic').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: dataUrl,
                    columns: [{
                            data: 'date',
                            name: 'date',
                            visible: false
                        },
                        {
                            data: 'group_month',
                            name: 'group_month',
                            visible: false
                        },
                        {
                            data: 'formatted_date',
                            name: 'date',
                            title: 'Tanggal Rekaman'
                        },
                        {
                            data: 'record_count',
                            name: 'record_count',
                            title: 'Jumlah Gambar'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            title: 'Aksi',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'desc'],
                        [0, 'desc']
                    ], // Urutkan berdasarkan bulan, lalu tanggal
                    rowGroup: {
                        dataSrc: 'group_month'
                    },
                    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    buttons: [
                        // Tombol export bisa ditambahkan di sini jika perlu
                    ],
                    language: {
                        search: "Cari Tanggal:",
                        lengthMenu: "_MENU_",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ hari rekaman",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
                $('div.head-label').html('<h5 class="card-title mb-0">Arsip Rekaman Tersimpan</h5>');
            });
        });
    </script>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="mb-0">Riwayat Kamera: {{ $camera->name }}</h4>
            <p class="text-muted mb-0">Daftar arsip rekaman yang dikelompokkan berdasarkan bulan.</p>
        </div>
        <a href="{{ route('log.history.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            <span class="align-middle">Kembali ke Pilih Kamera</span>
        </a>
    </div>

    <!-- DataTable -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                {{-- Header tabel akan dibuat oleh JavaScript --}}
            </table>
        </div>
    </div>
@endsection
