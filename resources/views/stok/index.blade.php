@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-sm btn-info mt-1">Import
                    Stok
                </button>
                {{-- <a class="btn btn-sm btn-primary mt-1" href="{{ url('stok/create') }}">Tambah</a> --}}
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i
                        class="fa fa-file-excel"></i> Export Stok</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i
                        class="fa fa-file-pdf"></i> Export Stok</a>
                <button onclick="modalAction('{{ url('stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    Tambah Ajax
                </button>
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
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">- Semua -</option>
                                @foreach($supplier as $item)
                                    <option value="{{ $item->supplier_id }}">{{ $item->supplier_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Supplier</small>
                        </div>
                        <div class="col-3">
                            <select class="form-control" id="barang_id" name="barang_id" required>
                                <option value="">- Semua -</option>
                                @foreach($barang as $item)
                                    <option value="{{ $item->barang_id }}">{{ $item->barang_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Barang</small>
                        </div>
                        <div class="col-3">
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">- Semua -</option>
                                @foreach($user as $item)
                                    <option value="{{ $item->user_id }}">{{ $item->username }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">User</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal Stok</th>
                    <th>Jumlah Stok</th>
                    <th>Supplier</th>
                    <th>Barang</th>
                    <th>User</th>
                    <th>Aksi</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
         data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var tableStok;
        $(document).ready(function () {
            tableStok = $('#table_stok').DataTable({
                // serverSide: true, jika ingin menggunakan server side processing
                serverSide: true,
                ajax: {
                    "url": "{{ url('stok/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.supplier_id = $('#supplier_id').val();
                        d.barang_id = $('#barang_id').val();
                        d.user_id = $('#user_id').val();
                    },
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "stok_tanggal",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_jumlah",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "supplier.supplier_nama",
                        className: "",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "barang.barang_nama",
                        className: "",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "user.username",
                        className: "",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#supplier_id').on('change', function () {
                tableStok.ajax.reload();
            });
            $('#barang_id').on('change', function () {
                tableStok.ajax.reload();
            });
            $('#user_id').on('change', function () {
                tableStok.ajax.reload();
            });

        });
    </script>
@endpush
