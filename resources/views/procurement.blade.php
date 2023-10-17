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
    <!-- Modal -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formItemDetails">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemDetailsModalLabel">Add New Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="txtName" name="name" autocomplete="false">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="internalCod" class="col-sm-2 col-form-label">Internal Code</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="txtInternalCod" name="internalCod" autocomplete="false">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="price" class="col-sm-2 col-form-label">Price</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <input type="number" autocomplete="false" class="form-control" name="price"
                                        id="txtPrice" aria-label="Actual price">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="module">

        // Reset the form before showing the modal
        $('#itemDetailsModal').on('showJus.bs.modal', function (event) { 
            $("#txtName").val("");
            $("#txtIntervalCod").val("");
            $("#txtPrice").val("");
        });

        $("#formItemDetails").on("submit", function(event) {
            event.preventDefault();
            console.log("Enviando!");
        });
        
        $('#formItemDetails').validate({
            rules: {
                name: {
                    required: true,
                },
                internalCod: {
                    required: true,
                    minlength: 5
                },
                price: {
                    required: true,
                    min:0
                },
            },
            messages: {
                name: {
                    required: "Please, enter a name for this Item.",
                },
                internalCod: {
                    required: "Please, enter a valid interval code.",
                    minlength: "The code should have at least 5 characters."
                },
                price: {
                    required: "Please, enter the price of the item."
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback col-sm-10 ml-auto');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

    </script>
@endpush
