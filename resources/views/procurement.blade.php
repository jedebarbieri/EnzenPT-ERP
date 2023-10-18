@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 justify-content-between">
                    <div class="col-sm-6">
                        <h1 class="m-0">Procurement</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemDetailsModal">
                            <i class="fa fa-plus"></i> New Item
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid p-3">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Item Prices</h3>
                            </div>
                            <div class="card-body">
                                <table id="itemsTable" class="table table-bordered table-hover"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('app.components.ItemModal', [
        'modalId' => 'itemDetailsModal',
    ])
@endsection

@push('page_scripts')
    <script type="module">
        $('#itemsTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,

            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 20, 50],

            "ajax": {
                "url": "/api/items",
                "type": "GET",
                "dataSrc": function ( json ) {
                    json.recordsFiltered = json.metadata.recordsFiltered;
                    json.recordsTotal = json.metadata.recordsTotal;
                    return json.data;
                }
            },
            "columns": [{
                    "data": "id",
                    "title": "Id",
                    "visible": false // Oculta la columna "Id"
                },
                {
                    "data": "name",
                    "title": "Name"
                },
                {
                    "data": "internalCod",
                    "title": "Internal Code"
                },
                {
                    "data": "unitPrice",
                    "title": "Unit Price",
                    "render": function(data, type, row) {
                        // Formatear el valor como moneda en euros
                        return new Intl.NumberFormat('pt-PT', {
                            style: 'currency',
                            currency: 'EUR'
                        }).format(data);
                    }
                }
            ],
            "columnDefs": [
                {
                    "targets": [3], // Índice de la columna "Unit Price"
                    "className": "text-right" // Clase de alineación a la derecha
                }
            ]
        });
    </script>
@endpush
