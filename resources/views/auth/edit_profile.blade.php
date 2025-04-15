@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <form method="POST" action="{{ url('/profile/update/' . $user->user_id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_user">ID User</label>
                                    <input type="text" class="form-control" id="id_user" name="id_user"
                                        value="{{ old('id_user', $user->user_id) }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="{{ old('nama', $user->nama) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username', $user->username) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="profile_image">Gambar Profil</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profile_image" name="profile_image" accept="image/*">
                                            <label class="custom-file-label" for="profile_image">Pilih file</label>
                                        </div>
                                    </div>

                                    @php
                                        $currentImage = null;
                                        $possibleExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                                        foreach ($possibleExtensions as $ext) {
                                            $filePath = 'admin/user_' . $user->user_id . '.' . $ext;
                                            if (file_exists(public_path($filePath))) {
                                                $currentImage = $filePath;
                                                break;
                                            }
                                        }
                                    @endphp

                                    <div class="mt-2">
                                        @if ($currentImage)
                                            <img class="profile-user-img img-fluid img-circle"
                                                 src="{{ asset($currentImage) }}"
                                                 alt="Current profile picture">
                                        @else
                                            <img class="profile-user-img img-fluid img-circle"
                                                 src="{{ asset('images/default-profile.jpg') }}"
                                                 alt="Default profile picture">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password Baru</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="text-muted">Biarkan kosong apabila tidak ingin mengganti password</small>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ url('/profile/' . Auth::user()->user_id) }}" class="btn btn-default">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }

            $('form').on('submit', function(e) {
                e.preventDefault();

                const fileInput = $('#profile_image')[0];
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];

                    if (!file.type.match('image.*')) {
                        Swal.fire('Error', 'Hanya file gambar yang diperbolehkan.', 'error');
                        return;
                    }

                    const fileSize = file.size / 1024 / 1024;
                    if (fileSize > 2) {
                        Swal.fire('File terlalu besar', 'Ukuran gambar tidak boleh lebih dari 2MB.',
                            'error');
                        return;
                    }
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Perubahan akan disimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, simpan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            @if ($errors->has('username'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ $errors->first('username') }}',
                });
            @endif

            $('#profile_image').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choose file');

                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.profile-user-img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
