@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Penjualan</h3>
                <div class="card-tools">
                    <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info">Import Penjualan</button>
                    <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan</a>
                    <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Penjualan</a>
                    <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
                </div>
            </div>
            <div class="card-body">
                <div id="filter" class="filter form-horizontal filter-date p-2 border-bottom mb-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-sm row text-sm mb-0">
                                <div class="col-md-1 col-form-label">
                                    <label for="filter_user">Filter</label>
                                </div>
                                <div class="col-md-3">
                                    <select name="filter_user" class="form-control form-control-sm filter_user">
                                        <option value="">-- Semua --</option>
                                        @foreach($user as $l)
                                            <option value="{{ $l->user_id }}">{{ $l->username }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">User</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Pembeli</th>
                            <th>Kode Penjualan</th>
                            <th>Tanggal Penjualan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
    </section>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var tablePenjualan;
        $(document).ready(function() {
            tablePenjualan = $('#table-penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.user_id = $('.filter_user').val(); // Perbaiki filter menggunakan class filter_user
                        console.log('Filter user_id:', $('.filter_user').val());
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "username", // Ubah dari "user.username" menjadi "username"
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "pembeli",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "penjualan_kode",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "penjualan_tanggal",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table-penjualan_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) {
                    tablePenjualan.search(this.value).draw();
                }
            });

            $('.filter_user').change(function() {
                tablePenjualan.draw();
            });
        });
    </script>
@endpush
