@extends('layouts/layoutMaster')

@section('title', 'Pengaturan Akun')

{{-- Tambahkan script jika menggunakan Alpine.js untuk notifikasi, atau pastikan sudah terload di layout induk --}}
@section('page-script')
    <script>
        // Pastikan Alpine.js/jQuery dimuat untuk fungsionalitas ini
        document.addEventListener('alpine:init', () => {
            Alpine.store('statusMessage', {
                show: false,
                message: '',
                type: 'success', // success, danger, warning

                showStatus(message, type = 'success') {
                    this.message = message;
                    this.type = type;
                    this.show = true;
                    setTimeout(() => {
                        this.show = false;
                    }, 3000);
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="mb-4">{{ __('Pengaturan Akun') }}</h4>

    <div class="row">
        <div class="col-lg-12">
            {{-- Notifikasi Umum --}}
            @if (session('status') === 'profile-updated' || session('status') === 'password-updated' || session('status') === 'retention-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="ti ti-check me-2"></i> {{ session('status') === 'retention-updated' ? __('Pengaturan penyimpanan berhasil diperbarui.') : __('Tersimpan.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="space-y-6">

                {{-- 1. INFORMASI PROFIL --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Informasi Profil') }}</h5>
                        <small class="text-muted">{{ __("Perbarui nama, email, dan status verifikasi Anda.") }}</small>
                    </div>

                    <div class="card-body">
                        <section>
                            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                @csrf
                            </form>

                            <form method="post" action="{{ route('profile.update') }}" class="row g-3">
                                @csrf
                                @method('patch')

                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">{{ __('Nama') }}</label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required autocomplete="username" />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                        <div class="mt-3">
                                            <div class="alert alert-warning p-2" role="alert">
                                                <p class="mb-1 text-sm">
                                                    {{ __('Alamat email Anda belum diverifikasi.') }}
                                                    <button form="send-verification"
                                                        class="btn btn-sm btn-link p-0 align-baseline text-decoration-underline"
                                                        style="color: inherit;">
                                                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                                    </button>
                                                </p>
                                                @if (session('status') === 'verification-link-sent')
                                                    <p class="mt-1 font-medium text-success text-sm">
                                                        {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-12 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary me-2">
                                        {{ __('Simpan Informasi') }}
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>

                {{-- 2. FORMULIR GANTI PASSWORD --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Perbarui Kata Sandi') }}</h5>
                        <small class="text-muted">{{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.') }}</small>
                    </div>
                    <div class="card-body">
                        <section>
                            <form method="post" action="{{ route('password.update') }}" class="row g-3">
                                @csrf
                                @method('put')

                                <div class="col-12 col-md-4">
                                    <label for="update_password_current_password" class="form-label">{{ __('Kata Sandi Saat Ini') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="update_password_current_password" name="current_password"
                                            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                            autocomplete="current-password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                    @error('current_password', 'updatePassword')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="update_password_password" class="form-label">{{ __('Kata Sandi Baru') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="update_password_password" name="password"
                                            class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                            autocomplete="new-password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                    @error('password', 'updatePassword')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="update_password_password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                                            class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                            autocomplete="new-password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                    @error('password_confirmation', 'updatePassword')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary me-2">
                                        {{ __('Simpan Kata Sandi') }}
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>

                {{-- 3. [BARU] PENGATURAN RETENSI PENYIMPANAN --}}
                <div class="card mb-4 border-start border-primary border-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Pengaturan Penyimpanan Kamera') }}</h5>
                        <small class="text-muted">{{ __('Tentukan berapa lama gambar CCTV Anda akan disimpan sebelum dihapus secara otomatis.') }}</small>
                    </div>
                    <div class="card-body">
                        <section>
                            <form method="post" action="{{ route('profile.update-retention') }}" class="row g-3">
                                @csrf
                                @method('patch')

                                <div class="col-12 col-md-6">
                                    <label for="retention_days" class="form-label">{{ __('Batas Penyimpanan Gambar (Hari)') }}</label>
                                    <div class="input-group">
                                        <input type="number" id="retention_days" name="retention_days"
                                            class="form-control @error('retention_days') is-invalid @enderror"
                                            value="{{ old('retention_days', $user->retention_days ?? 30) }}"
                                            min="1" max="365" required />
                                        <span class="input-group-text">{{ __('Hari') }}</span>
                                        @error('retention_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <p class="mt-2 mb-0 small text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        {{ __('Gambar yang lebih tua dari durasi ini akan dihapus permanen dari storage setiap hari pada pukul 00:00.') }}
                                    </p>
                                </div>

                                <div class="col-12 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary me-2">
                                        {{ __('Simpan Pengaturan Penyimpanan') }}
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>

                {{-- 4. FORMULIR HAPUS AKUN --}}
                <div class="card mb-4 border border-2 border-danger">
                    <div class="card-header">
                        <h5 class="mb-0 text-danger">{{ __('Hapus Akun') }}</h5>
                        <small class="text-muted">{{ __('Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Harap unduh data apa pun yang ingin Anda simpan sebelum melanjutkan.') }}</small>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#confirmUserDeletionModal">
                            {{ __('Hapus Akun') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1"
        aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true"
        @if ($errors->userDeletion->isNotEmpty()) style="display: block;" @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">
                            {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            {{ __('Mohon masukkan kata sandi Anda untuk mengkonfirmasi penghapusan permanen akun.') }}
                        </p>

                        <div class="mt-3 form-password-toggle">
                            <label for="password_delete" class="form-label visually-hidden">{{ __('Password') }}</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_delete" name="password"
                                    class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                    placeholder="{{ __('Password') }}" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Hapus Akun') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
                deleteModal.show();
                document.getElementById('password_delete').focus();
            });
        </script>
    @endif
@endsection
