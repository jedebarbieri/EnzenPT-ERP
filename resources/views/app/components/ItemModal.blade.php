<!-- resources/views/app/components/ItemModal.blade.php -->

<div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-labelledby="{{$modalId}}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formItemDetails" role="form">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{$modalId}}Label">Add New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtName" name="name"
                                autocomplete="false" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="internalCod" class="col-sm-2 col-form-label">Internal Code</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtInternalCod" name="internalcod"
                                autocomplete="false" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="price" class="col-sm-2 col-form-label">Price</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                <input type="number" autocomplete="false" class="form-control" name="unit_price"
                                    value="0.00" id="txtPrice" aria-label="Actual price" />
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

<template id="modalAlertTemplate">
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none">
        <div class="message"></div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>


@push('page_scripts')

<script type="module">

    let itemModal = $("#{{$modalId}}");

    // Reset the form before showing the modal
    itemModal.on('show.bs.modal', function(event) {
        $("#txtName").val("");
        $("#txtIntervalCod").val("");
        $("#txtPrice").val("0.00");
    });

    let messageBox = itemModal.find('.alert');

    messageBox.on('close.bs.alert', function () {
        // Ocultar la alerta pero mantenerla en el DOM
        console.log('cerrar');
        $(this).hide();
    });

    $('#formItemDetails').validate({
        rules: {
            name: {
                required: true,
            },
            internalCod: {
                minlength: 5
            },
            unitPrice: {
                required: true,
                min: 0
            },
        },
        messages: {
            name: {
                required: "Please, enter a name for this Item.",
            },
            internalCod: {
                minlength: "The code should have at least 5 characters."
            },
            unitPrice: {
                required: "Please, enter the price of the item."
            },
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback col-sm-10 ml-auto');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            var formData = $(form).serialize();
            axios.post('/api/items', formData)
                .then(function(response) {
                    // Manejar la respuesta exitosa
                    console.log(response.data);
                    itemModal.modal('hide');
                })
                .catch(function(error) {
                    let messageBox = itemModal.find('.modal-body .alert');
                    if (messageBox.length == 0) {
                        messageBox = $("#modalAlertTemplate").contents().clone().removeAttr('id');
                        itemModal.find('.modal-body').prepend(messageBox);
                    }
                    // Manejar errores
                    if (error.response) {
                        // La solicitud fue hecha y el servidor respondió con un código de estado que no está en el rango 2xx
                        console.error(error.response.data);
                        console.error(error.response.status);
                        console.error(error.response.headers);
                        // Imprimimos la respuesta del servidor en la ventana
                        messageBox.find('.message').text(error.response.data.message);
                        messageBox.show();
                    } else if (error.request) {
                        // La solicitud fue hecha pero no se recibió ninguna respuesta
                        console.log("Sin respuesta");
                        console.error(error.request);
                    } else {
                        // Algo sucedió en el proceso de configuración de la solicitud que generó el error
                        console.error('Error', error.message);
                    }
                });
        }
    });
</script>
@endpush