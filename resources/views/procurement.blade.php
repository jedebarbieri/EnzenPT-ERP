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
                                <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('app.components.ItemModal', [
        'modalId' => 'itemDetailsModal'
    ])
@endsection

