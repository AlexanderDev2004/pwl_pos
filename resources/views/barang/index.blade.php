@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('barang/import') }}')" class="btn btninfo">Import Barang</button>
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah</a>
                <button onclick="modalAction('{{ url('barang/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah
                    Ajax
                </button>
                <a href="{{ route('barang.export_excel') }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export Barang Excel
                </a>
                <a href="{{ url('barang.export_pdf') }}" class="btn btn-warning"><i class="fa fa-file
                    pdf"></i> Export Barang PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted"> Kategori barang</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>kategori</th>
                        <th>barang kode</th>
                        <th>barang nama</th>
                        <th>harga beli</th>
                        <th>harga jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
            <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" databackdrop="static"
                data-keyboard="false" data-width="75%" aria-hidden="true"></div>
        @endsection
        @push('css')
        @endpush
        @push('js')
            <script>
                function modalAction(url = '') {
                    $('#myModal').load(url, function() {
                        $('#myModal').modal('show');
                    });
                }

                var dataUser;
                $(document).ready(function() {
                    dataUser = $('#table_user').DataTable({
                        serverSide: true,
                        ajax: {
                            "url": "{{ url('barang/list') }}",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d) {
                                d.kategori_id = $('#kategori_id').val();
                            }
                        },
                        columns: [{
                            data: "DT_RowIndex",
                            className: "text-center",
                            orderable: false,
                            searchable: false
                        }, {
                            data: "kategori.kategori_nama",
                            orderable: true,
                            searchable: true
                        }, {
                            data: "barang_kode",
                            orderable: true,
                            searchable: true
                        }, {
                            data: "barang_nama",
                            orderable: true,
                            searchable: true
                        }, {
                            data: "harga_beli",
                            orderable: true,
                            searchable: true
                        }, {
                            data: "harga_jual",
                            orderable: true,
                            searchable: true
                        }, {
                            data: "aksi",
                            orderable: false,
                            searchable: false
                        }]
                    });

                    $('#table_user_filter input').unbind().bind().on('keyup', function(e) {
                        if (e.keyCode == 13) {
                            dataUser.search(this.value).draw();
                        }
                    });

                    $('#kategori_id').change(function() {
                        dataUser.draw();
                    });
                });
            </script>
        @endpush
