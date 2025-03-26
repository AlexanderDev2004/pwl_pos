<form action="{{ url('/user/store_ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" id="level_id" class="form-control" required>
                        <option value="">- Pilih Level -</option>
                        @foreach ($level as $l)
                            <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                    <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small id="error-password" class="error-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-tambah").submit(function(event) {
            event.preventDefault(); // Mencegah reload halaman

            var form = this;
            var formData = new FormData(form);
            formData.append('_token', '{{ csrf_token() }}'); // Tambahkan CSRF token

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(".error-text").text(''); // Reset error
                },
                success: function(response) {
                    console.log(response); // Debugging

                    if (response.status) {
                        $('#modal-master').modal('hide'); // Tutup modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });

                        // Reload data table jika ada
                        if (typeof dataUser !== "undefined") {
                            dataUser.ajax.reload();
                        }

                        form.reset(); // Reset form setelah sukses
                    } else {
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // Debugging error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal menambahkan user, coba lagi.'
                    });
                }
            });
        });
    });
</script>
