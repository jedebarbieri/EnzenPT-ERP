@php
    use App\Models\Procurement\ItemCategory;
    $categoriesList = ItemCategory::getAllMainCategories();

    $selCategoryId = $modalId . 'ItemCategorySelect';
@endphp
<!-- resources/views/app/components/ItemModal.blade.php -->

<div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-labelledby="{{$modalId}}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="{{$modalId}}FormItemDetails" role="form">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{$modalId}}Label">Add New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="id" id="hdId" value=""/>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="internalCod" class="col-sm-2 col-form-label">Internal Code</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtInternalCod" name="internal_cod"
                                autocomplete="false" />
                        </div>
                    </div>
                    @component('app.components.SelectDropdown', [
                        'id' => $selCategoryId,
                        'name' => 'item_category_id',
                        'dropdownParent' => $modalId,
                        'label' => 'Category',
                        'elements' => $categoriesList,
                        'displayFunc' => function($category) {
                            return $category->prefixCode . " - " . $category->name;
                        }
                    ])
                    @endcomponent
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtName" name="name"
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

    var itemModal = $("#{{$modalId}}");
    var formModal = $('#{{$modalId}}FormItemDetails');
    var title = $("#{{$modalId}}Label");
    var messageBox = itemModal.find('.alert');

    messageBox.on('close.bs.alert', function () {
        // Ocultar la alerta pero mantenerla en el DOM
        $(this).hide();
    });

    var validator = formModal.validate({
        rules: {
            name: {
                required: true,
            },
            internal_cod: {
                minlength: 5
            },
            unit_price: {
                required: true,
                min: 0
            },
        },
        messages: {
            name: {
                required: "Please, enter a name for this Item.",
            },
            internal_cod: {
                minlength: "The code should have at least 5 characters."
            },
            unit_price: {
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
            var formData = window.arrayToJsonObject($(form).serializeArray());
            let req;
            if (formData.id) {
                req = axios.put(`/api/items/${formData.id}`, formData);
            } else {
                req = axios.post("/api/items", formData);
            }
            req
                .then(function(response) {
                    // Manejar la respuesta exitosa
                    document.dispatchEvent(new CustomEvent('itemsTable.reloadTable'));
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

    // Reset the form before showing the modal
    itemModal.on('hidden.bs.modal', function(event) {
        validator.resetForm();
        formModal[0].reset();
        formModal.find("#{{ $selCategoryId }}").val(null).trigger('change');
        title.html("Add New Item");
        formModal.find('.error').removeClass("error");
        formModal.find('.is-invalid').removeClass("is-invalid");
        formModal.find('.form-control-feedback').remove();
    });

    // Evento para cargar este modal con la información proporcionada
    document.addEventListener('itemModal.loadData', (event) => {
        let itemData = event.detail.data;
        // @TODO Show data incongruency error
        if (itemData.category === null) {
            console.error("This item does not have any category. This is a data incongruency error. Please contact the System Administrator.");
            return;
        }
        title.html("Edit Item");
        formModal.find("#hdId").val(itemData.id);
        formModal.find("#txtName").val(itemData.name);
        formModal.find("#txtInternalCod").val(itemData.internalCod);
        formModal.find("#txtPrice").val(itemData.unitPrice);        
        formModal.find("#{{ $selCategoryId }}").val(itemData.itemCategory.id).trigger('change');
        itemModal.modal('show');
    });

</script>
@endpush