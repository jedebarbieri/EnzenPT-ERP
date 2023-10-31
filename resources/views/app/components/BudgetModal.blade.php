@php
    use App\Models\Procurement\ItemCategory;
    $categoriesList = ItemCategory::getAllMainCategories();

    $selCategoryId = $modalId . 'ItemCategorySelect';
@endphp
<!-- resources/views/app/components/BudgetModal.blade.php -->

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xxl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">New Budget</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="{{ $modalId }}FormBudgetMainInfo" role="form">
                    <input type="hidden" name="id" id="hdId" value="" />
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <nav class="navbar p-0">
                                <h6 class="card-title text-bold">General Info</h6>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </nav>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="txtBudgetName" class="">Name</label>
                                        <input type="text" class="form-control" id="txtBudgetName" name="name"
                                            autocomplete="false" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="txtTotalPowerPick" class="">Total Power Pick</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" id="txtTotalPowerPick"
                                                name="total_power_pick" autocomplete="false" value="0.00" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">€/Wp</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="txtGainMargin" class="">Gain Margin</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" id="txtGainMargin"
                                                name="gain_margin" autocomplete="false" value="0.00" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="txtProjectName" class="">Project Name</label>
                                        <input type="text" class="form-control" id="txtProjectName"
                                            name="project_name" autocomplete="false" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="txtProjectNumber" class="">Project Number</label>
                                        <input type="text" class="form-control" id="txtProjectNumber"
                                            name="project_number" autocomplete="false" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="txtProjectLocation" class="">Project Location</label>
                                        <input type="text" class="form-control" id="txtProjectLocation"
                                            name="project_location" autocomplete="false" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-save mr-2"></i> Save
                            </button>
                        </div>

                    </div>
                </form>

                <div class="card">
                    <div class="card-header">
                        <nav class="navbar p-0">
                            <h6 class="card-title text-bold">Supplies List</h6>

                            <button type="button" class="btn btn-primary">
                                <i class="fa fa-plus mr-2"></i> Add Item
                            </button>
                            {{-- <form class="form-inline">
                                <input class="form-control mr-sm-2" type="search" placeholder="Search"
                                    aria-label="Search">
                                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                            </form> --}}
                        </nav>

                    </div>
                    <div class="card-body">

                        <table id="budgetDetailsTable" class="table table-hover table-valign-middle"></table>

                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i> Add Item
                        </button>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
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
    <template id="buttonsOptionsPerBudgetDetailTemplate">
        <td class="text-right text-nowrap" style="width: 0%">
            <div class="button-container">
                <button class="btn btn-outline-danger btn-sm delete-btn" data-id="__ID__" title="Delete"><i
                        class="fas fa-trash"></i></button>
            </div>
        </td>
    </template>

    <script type="module">
        var budgetModal = $("#{{ $modalId }}");
        var formModal = $('#{{ $modalId }}FormBudgetMainInfo');
        var title = $("#{{ $modalId }}Label");
        var messageBox = budgetModal.find('.alert');

        // Reference to the datatable instance to store all the budget details
        var budgetDetailsTable = null;

        messageBox.on('close.bs.alert', function() {
            // Ocultar la alerta pero mantenerla en el DOM
            $(this).hide();
        });

        var validator = formModal.validate({
            rules: {
                name: {
                    required: true,
                },
                total_power_pick: {
                    min: 0.00
                },
                gain_margin: {
                    min: 0.00
                },
                project_name: {
                    required: true,
                    minlength: 3
                },
                project_number: {
                    required: false,
                    minlength: 3
                },
                project_location: {
                    required: false,
                    minlength: 3
                }
            },
            messages: {
                name: {
                    required: "Please, enter a name for this Budget.",
                },
                total_power_pick: {
                    min: "Enter a valid value."
                },
                gain_margin: {
                    min: "Enter a valid value."
                },
                project_name: {
                    minlength: "3 characters minimum."
                },
                project_number: {
                    minlength: "3 characters minimum."
                },
                project_location: {
                    minlength: "3 characters minimum."
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
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
                    req = axios.put(`/api/budgets/${formData.id}`, formData);
                } else {
                    req = axios.post("/api/budgets", formData);
                }
                req
                    .then(function(response) {
                        // Manejar la respuesta exitosa
                        document.dispatchEvent(new CustomEvent('budgetsTable.reloadTable'));
                        budgetModal.modal('hide');
                    })
                    .catch(function(error) {
                        let messageBox = budgetModal.find('.modal-body .alert');
                        if (messageBox.length == 0) {
                            messageBox = $("#modalAlertTemplate").contents().clone().removeAttr('id');
                            budgetModal.find('.modal-body').prepend(messageBox);
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
        budgetModal.on('hidden.bs.modal', function(event) {
            validator.resetForm();
            formModal[0].reset();
            title.html("New Budget");
            formModal.find('.error').removeClass("error");
            formModal.find('.is-invalid').removeClass("is-invalid");
            formModal.find('.form-control-feedback').remove();
        });

        // Evento para cargar este modal con la información proporcionada
        document.addEventListener('budgetModal.loadData', (event) => {
            let budgetData = event.detail.data;
            title.html("Edit Budget");
            formModal.find("#hdId").val(budgetData.id);
            formModal.find("#txtBudgetName").val(budgetData.name);
            formModal.find("#txtTotalPowerPick").val(budgetData.totalPowerPick);
            formModal.find("#txtGainMargin").val(Math.round(budgetData.gainMargin * 10000) / 100);
            formModal.find("#txtProjectName").val(budgetData.projectName);
            formModal.find("#txtProjectNumber").val(budgetData.projectNumber);
            formModal.find("#txtProjectLocation").val(budgetData.projectLocation);
            budgetModal.modal('show');

            // Loading the budget details: list of items

            if (!budgetDetailsTable) {
                budgetDetailsTable = $('#budgetDetailsTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "serverSide": true,
                    "paging": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,

                    "ajax": {
                        "url": `/api/budgets/${budgetData.id}`,
                        "type": "GET",
                        "dataSrc": "data.budget.budgetDetails"
                    },
                    "columns": [{
                            "data": "id",
                            "title": "Id",
                            "visible": false // Oculta la columna "Id"
                        },
                        {
                            "data": "item.internalCod",
                            "title": "Cod.",
                            "class": "text-nowrap",
                            "width": "0%",
                        },
                        {
                            "data": "item.name",
                            "title": "Name"
                        },
                        {
                            "data": "unitPrice",
                            "title": "Unit P.",
                            "class": "unit_price",
                            "width": "0%",
                        },
                        {
                            "data": "quantity",
                            "title": "Qty",
                            "class": "quantity text-right",
                            "width": "0%",
                        },
                        {
                            "data": "taxPercentage",
                            "title": "IVA %",
                            "class": "taxPercentage text-right",
                            "width": "0%",
                        },
                        {
                            "data": "discount",
                            "title": "Disc.",
                            "class": "discount text-right",
                            "width": "0%",
                        },
                        {
                            "data": "sellPrice", // acá es donde tengo problemas
                            "title": "Selling Price",
                            "class": "sellPrice text-right",
                            "width": "0%",
                        },
                    ],
                    "columnDefs": [{
                        // Apply classes only to some cells
                        "targets": [3, 4, 5, 6, 7],
                        "createdCell": function(td, cellData, rowData, row, col) {
                            if ($(td).closest('tbody')) {
                                $(td).addClass(' py-0 pr-0');
                            }
                        }
                    }],
                    headerCallback: function(thead, data, start, end, display) {
                        if ($(thead).find("#optCol").length === 0) {
                            $(thead).prepend($('<th/>', {
                                id: "optCol",
                                class: "text-center"
                            }));
                        }
                    },
                    createdRow: function(row, data, index) {
                        // Clonar el contenido del template
                        let clonedContent = $("#buttonsOptionsPerBudgetDetailTemplate").contents()
                            .clone();

                        // Reemplazar los placeholders con valores dinámicos
                        clonedContent.find('.edit-btn').attr('data-id', data.id);

                        // Añade la clase deseada a la fila
                        $(row).prepend(clonedContent);

                        // Añadimos atributo de data-id a cada fila
                        $(row).attr("data-id", data.id);

                        // Input for the Unit Price
                        let unitPriceCell = $(row).find("td.unit_price");
                        const unitPriceInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: unitPriceCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                        });
                        unitPriceCell.empty().append(unitPriceInpEditable.inputElement);

                        // Input for Quantity
                        let quantityCell = $(row).find("td.quantity");
                        const quantityInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: quantityCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_DECIMAL_MASK_OPTIONS,
                        });
                        quantityCell.empty().append(quantityInpEditable.inputElement);

                        // Input for the IVA
                        let taxPercentageCell = $(row).find("td.taxPercentage");
                        const taxPercentageInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            valueType: "percentage",
                            nodeAttributes: {
                                value: taxPercentageCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_PERCENTAGE_MASK_OPTIONS,
                        });
                        taxPercentageCell.empty().append(taxPercentageInpEditable.inputElement);

                        // Input for the Discount
                        let discountCell = $(row).find("td.discount");
                        const discountInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: discountCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                        });
                        discountCell.empty().append(discountInpEditable.inputElement);

                        // Input for the Sell Pricie
                        let sellPriceCell = $(row).find("td.sellPrice");
                        const sellPriceInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: sellPriceCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                        });
                        sellPriceCell.empty().append(sellPriceInpEditable.inputElement);

                    }
                });
            } else {
                // Acá quiero actualizar la ruta AJAX y refrescar la tabla.
                budgetDetailsTable.ajax.url(`/api/budgets/${budgetData.id}`).load();
            }

        });

        /**
         * This class will update one value using a PATCH service.
         * It will call the service on ENTER press, blurout, TAB press.
         * It will show a loading animation.
         * It will show an error message if necesssary
         *  
         **/
        class InputEditable {
            /**
             * Associated input element for the component.
             * @type {HTMLElement}
             * @public
             */
            inputElement = null;

            /**
             * API service used for updates.
             * @type {string}
             * @public
             */
            apiService = "";

            /**
             * Object used to send aditional data to the service
             * @type {Object}
             * @public
             */
            apiData = {};

            /**
             * Callback invoked when the value changes.
             * @type {Function|null}
             * @public
             */
            onChangeCallback = null;

            /**
             * Callback invoked when the component loses focus.
             * @type {Function|null}
             * @public
             */
            onBlurCallback = null;

            /**
             * Attributes of the node used to render the component.
             * @type {Object}
             * @public
             */
            nodeAttributes = {};

            /**
             * Value associated with the editable component.
             * This is the abstract value (the real value). It is not the formatted string.
             * @type {string}
             * @private
             */
            _rawValue = null;

            /**
             * This is the type of the real value. It can be "string" or "number"
             * @type {string}
             * @public
             */
            valueType = "string";

            /**
             * Determine if the input will show a mask or not.
             * @type {Inputmask}
             * @public
             */
            inputmask = null;

            static DEFAULT_CURRENCY_MASK_OPTIONS = {
                alias: 'currency',
                groupSeparator: ' ',
                radixPoint: '.',
                autoGroup: true,
                rightAlign: true,
                digits: 2,
                suffix: ' €',
                prefix: '',
                placeholder: '0.00',
            };

            static DEFAULT_PERCENTAGE_MASK_OPTIONS = {
                alias: 'numeric',
                groupSeparator: ' ',
                radixPoint: '.',
                autoGroup: true,
                rightAlign: true,
                digits: 2,
                suffix: ' %',
                prefix: '',
                placeholder: '0,00',
            };

            static DEFAULT_DECIMAL_MASK_OPTIONS = {
                alias: 'numeric',
                groupSeparator: ' ',
                radixPoint: '.',
                autoGroup: true,
                rightAlign: true,
                digits: 2,
                suffix: '',
                prefix: '',
                placeholder: '0.00',
            };

            /**
             * Constructor for the InputEditable class.
             * @param {Object} options - Configuration options for the component.
             * @param {HTMLElement} options.inputElement - Associated input element.
             * @param {string} options.apiService - API service for updates.
             * @param {Function|null} options.onChangeCallback - Value change callback.
             * @param {Function|null} options.onBlurCallback - Blur callback.
             * @param {string} options.nodeType - Type of node for rendering.
             * @param {Object} options.nodeAttributes - Node attributes for rendering.
             * @param {Object} options.maskOptions - Options to create the Inputmask instance if it is necesary
             * @param {*} options.value - Initial value of the component.
             */
            constructor(options) {
                Object.assign(this, options);

                if (!this.inputElement) {
                    // We will construct the default element

                    this.nodeAttributes = {
                        ...{
                            "type": "text",
                            "class": `content-editable ${this.additionalClasses ?? ''}`,
                        },
                        ...options.nodeAttributes
                    };

                    this.inputElement = $(`<input/>`, this.nodeAttributes);
                }

                if (this.maskOptions && !this.inputmask) {
                    this.inputmask = new Inputmask(this.maskOptions);
                    this.inputmask.$el = this.inputElement;
                    this.inputmask.mask(this.inputElement);
                }

                // Doing a cross reference
                this.inputElement.data("InputEditableInstance", this);
                this.value = this.nodeAttributes.value;

                this.inputElement.change((event) => {
                    let inputVal = event.currentTarget.inputmask.unmaskedvalue();
                    if (this.valueType == "percentage") {
                        inputVal /= 100;
                    }
                    this.value = inputVal;
                });

                // Now we will add listeners to dispatch the patchEvent
                this.inputElement.blur((event) => {
                    this.patchEvent();
                });
            }

            /**
             * Sets a raw value for this instance and will show in the input the formatted version
             */
            set value(val) {
                this._rawValue = val;

                let showVal = this._rawValue
                if (this.valueType == "percentage") {
                    showVal = this._rawValue * 100;
                }
                this.inputElement.val(showVal);

                this.inputmask.mask(this.inputElement);
            }


            /**
             * Gets the real number on the input if the type is number.
             * @returns {string} the raw value
             */
            get value() {
                // Removes separators and decimal characters. 
                return this._rawValue;
            }

            /**
             * Executes the API service to update the data.
             * @public
             */
            patchEvent() {
                console.log("saving... NOT IMPLEMENTED YET");
                console.log(this.value);
                return;
                let req;
                axios.patch(this.apiService, this.apiData)
                    .then(function(response) {
                        // Manejar la respuesta exitosa

                    })
                    .catch(function(error) {
                        // Manejamos el error
                    });
            }
        }
    </script>
@endpush
