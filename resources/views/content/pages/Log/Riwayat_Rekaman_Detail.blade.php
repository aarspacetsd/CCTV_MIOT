@extends('layouts/layoutMaster')

@section('title', 'Detail Rekaman - ' . \Carbon\Carbon::parse($formattedDate)->format('j M Y'))

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
        // Gunakan event listener 'load' untuk memastikan semua skrip (termasuk jQuery) sudah dimuat
        window.addEventListener('load', function() {
            $(function() {
                // URL untuk mengambil data dari controller
                const dataUrl =
                    "{{ route('log.history.data', ['camera' => $camera->id, 'date' => $formattedDate]) }}";

                // Inisialisasi DataTable
                var dt_basic = $('.datatables-basic').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: dataUrl,
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        // Kolom baru untuk data grouping, akan disembunyikan
                        {
                            data: 'group_hour',
                            name: 'group_hour',
                            visible: false
                        },
                        {
                            data: 'path',
                            name: 'path',
                            title: 'Pratinjau',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<img src="' + data +
                                    '" alt="Pratinjau" class="img-thumbnail" style="width: 100px; height: 60px; object-fit: cover;">';
                            }
                        },
                        {
                            data: 'time',
                            name: 'captured_at',
                            title: 'Waktu Rekaman'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            title: 'Aksi',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<button class="btn btn-sm btn-outline-primary btn-view-image" data-img-src="' +
                                    full.full_path + '" data-timestamp="' + full.time +
                                    '">Lihat</button>';
                            }
                        }
                    ],
                    // Urutkan berdasarkan grup jam, lalu berdasarkan waktu
                    order: [
                        [1, 'asc'],
                        [0, 'desc']
                    ],
                    // Aktifkan Row Grouping
                    rowGroup: {
                        dataSrc: 'group_hour'
                    },
                    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    buttons: [{
                        extend: 'collection',
                        className: 'btn btn-label-primary dropdown-toggle me-2',
                        text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                        buttons: [{
                                extend: 'print',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'csv',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'excel',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'pdf',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'copy',
                                className: 'dropdown-item'
                            }
                        ]
                    }],
                    language: {
                        search: "Cari:",
                        lengthMenu: "_MENU_",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ rekaman",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
                $('div.head-label').html('<h5 class="card-title mb-0">Daftar Rekaman</h5>');

                // Event listener untuk tombol "Lihat"
                $('.datatables-basic tbody').on('click', '.btn-view-image', function() {
                    var imgSrc = $(this).data('img-src');
                    var timestamp = $(this).data('timestamp');
                    $('#modalImage').attr('src', imgSrc);
                    $('#imageModalLabel').text('Pratinjau Rekaman: ' + timestamp + ' WIB');
                    $('#imageModal').modal('show');
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

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                {{-- Header tabel akan dibuat oleh JavaScript --}}
            </table>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Pratinjau Rekaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded" alt="Gambar Ukuran Penuh">
                </div>
            </div>
        </div>
    </div>
@endsection
