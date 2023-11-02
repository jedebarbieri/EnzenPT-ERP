@php
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
                    <div class="card">
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

                            <button type="button" class="btn btn-primary addItemBtn">
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

                        <table id="{{ $modalId }}budgetDetailsTable"
                            class="table table-hover table-valign-middle"></table>

                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary addItemBtn">
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
        var budgetId = null;
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

        /**
         * Calculates the totals of this row. The row is the element <tr> of the table.
         * 
         * @param {jQuery} row
         */
        function calculateTotalRow(row) {
            let totalWOTax = row.calculablesColums.sellPriceInpEditable.value * row.calculablesColums.quantityInpEditable
                .value - row.calculablesColums.discountInpEditable.value;
            let totalWTax = totalWOTax + totalWOTax * row.calculablesColums.taxPercentageInpEditable.value;
            let mask = new Inputmask(InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS);
            $(row).find(".total-col").text(mask.format(totalWOTax.toFixed(2)));
            $(row).find(".total-tax-col").text(mask.format(totalWTax.toFixed(2)));
        }

        // Evento para cargar este modal con la información proporcionada
        document.addEventListener('budgetModal.loadData', (event) => {
            let budgetData = event.detail.data;
            const apiServiceBase = `/api/budgets/${budgetData.id}/budgetDetails/`;
            title.html("Edit Budget");
            formModal.find("#hdId").val(budgetId = budgetData.id);
            formModal.find("#txtBudgetName").val(budgetData.name);
            formModal.find("#txtTotalPowerPick").val(budgetData.totalPowerPick);
            formModal.find("#txtGainMargin").val(Math.round(budgetData.gainMargin * 10000) / 100);
            formModal.find("#txtProjectName").val(budgetData.projectName);
            formModal.find("#txtProjectNumber").val(budgetData.projectNumber);
            formModal.find("#txtProjectLocation").val(budgetData.projectLocation);
            budgetModal.modal('show');

            // Loading the budget details: list of items

            if (!budgetDetailsTable) {
                budgetDetailsTable = $('#{{ $modalId }}budgetDetailsTable').DataTable({
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
                            "class": "unit_price text-right",
                            "width": "0%",
                        },
                        {
                            "data": "sellPrice",
                            "title": "Selling Price",
                            "class": "sellPrice text-right",
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
                        if ($(thead).find("th.opt-col").length === 0) {
                            $(thead).prepend($('<th/>', {
                                class: "opt-col text-center"
                            }));
                        }
                        if ($(thead).find("th.total-col").length === 0) {
                            $(thead).append($('<th/>', {
                                class: "total-col text-right",
                                style: "width: 0%",
                                html: "Total WO/IVA",
                            }));
                        }
                        if ($(thead).find("th.total-tax-col").length === 0) {
                            $(thead).append($('<th/>', {
                                class: "total-tax-col text-right",
                                style: "width: 0%",
                                html: "Total W/IVA",
                            }));
                        }
                    },
                    createdRow: function(row, data, index) {
                        // Cloning the options col
                        let clonedContent = $("#buttonsOptionsPerBudgetDetailTemplate").contents()
                            .clone();

                        // Setting the id reference
                        clonedContent.find('.delete-btn').attr('data-id', data.id);

                        // Adding the options col
                        $(row).prepend(clonedContent);

                        // Adding the id reference
                        $(row).attr("data-id", data.id);

                        // List of all calculable columns in this row
                        row.calculablesColums = {
                            unitPriceInpEditable: null,
                            quantityInpEditable: null,
                            taxPercentageInpEditable: null,
                            discountInpEditable: null,
                            sellPriceInpEditable: null,
                        };

                        let apiService = `${apiServiceBase}${data.id}`;

                        // Input for the Unit Price
                        let unitPriceCell = $(row).find("td.unit_price");
                        row.calculablesColums.unitPriceInpEditable = new InputEditable({
                            nodeAttributes: {
                                value: unitPriceCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                            onChangeCallback: function(event) {
                                axios.patch(apiService, {
                                        id: data.id,
                                        unit_price: event.data.newValue,
                                    })
                                    .then(function(response) {
                                        // Manejar la respuesta exitosa
                                        calculateTotalRow(row);
                                    })
                                    .catch(function(error) {
                                        // Manejamos el error
                                    });
                            }
                        });
                        unitPriceCell.empty().append(row.calculablesColums.unitPriceInpEditable
                            .inputElement);

                        // Input for the Sell Pricie
                        let sellPriceCell = $(row).find("td.sellPrice");
                        row.calculablesColums.sellPriceInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: sellPriceCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                            onChangeCallback: function(event) {
                                axios.patch(apiService, {
                                        id: data.id,
                                        sell_price: event.data.newValue,
                                    })
                                    .then(function(response) {
                                        // Manejar la respuesta exitosa
                                        calculateTotalRow(row);
                                    })
                                    .catch(function(error) {
                                        // Manejamos el error
                                    });
                            }
                        });
                        sellPriceCell.empty().append(row.calculablesColums.sellPriceInpEditable
                            .inputElement);

                        // Input for Quantity
                        let quantityCell = $(row).find("td.quantity");
                        row.calculablesColums.quantityInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: quantityCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_DECIMAL_MASK_OPTIONS,
                            onChangeCallback: function(event) {
                                axios.patch(apiService, {
                                        id: data.id,
                                        quantity: event.data.newValue,
                                    })
                                    .then(function(response) {
                                        // Manejar la respuesta exitosa
                                        calculateTotalRow(row);
                                    })
                                    .catch(function(error) {
                                        // Manejamos el error
                                    });
                            }
                        });
                        quantityCell.empty().append(row.calculablesColums.quantityInpEditable
                            .inputElement);

                        // Input for the IVA
                        let taxPercentageCell = $(row).find("td.taxPercentage");
                        row.calculablesColums.taxPercentageInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            valueType: "percentage",
                            nodeAttributes: {
                                value: taxPercentageCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_PERCENTAGE_MASK_OPTIONS,
                            onChangeCallback: function(event) {
                                axios.patch(apiService, {
                                        id: data.id,
                                        tax_percentage: event.data.newValue,
                                    })
                                    .then(function(response) {
                                        // Manejar la respuesta exitosa
                                        calculateTotalRow(row);
                                    })
                                    .catch(function(error) {
                                        // Manejamos el error
                                    });
                            }
                        });
                        taxPercentageCell.empty().append(row.calculablesColums.taxPercentageInpEditable
                            .inputElement);

                        // Input for the Discount
                        let discountCell = $(row).find("td.discount");
                        row.calculablesColums.discountInpEditable = new InputEditable({
                            apiService: `api/budgets/${budgetData.id}/budgetDetails/${data.id}`,
                            nodeAttributes: {
                                value: discountCell.text(),
                            },
                            additionalClasses: "text-right",
                            maskOptions: InputEditable.DEFAULT_CURRENCY_MASK_OPTIONS,
                            onChangeCallback: function(event) {
                                axios.patch(apiService, {
                                        id: data.id,
                                        discount: event.data.newValue,
                                    })
                                    .then(function(response) {
                                        // Manejar la respuesta exitosa
                                        calculateTotalRow(row);
                                    })
                                    .catch(function(error) {
                                        // Manejamos el error
                                    });
                            }
                        });
                        discountCell.empty().append(row.calculablesColums.discountInpEditable
                            .inputElement);

                        // Adding the total columns
                        $(row)
                            .append(
                                $('<td/>', {
                                    class: "total-col text-right text-nowrap"
                                })
                            ).append(
                                $('<td/>', {
                                    class: "total-tax-col text-right text-nowrap"
                                })
                            );

                        calculateTotalRow(row);
                    }
                });
            } else {
                budgetDetailsTable.ajax.url(`/api/budgets/${budgetData.id}`).load();
            }

        });

        $("#{{ $modalId }}budgetDetailsTable").on('click', '.delete-btn', function(event) {
            var budgetDetailId = $(this).data('id');

            // Aquí puedes realizar la lógica de eliminación
            axios.delete(`api/budgets/${budgetId}/budgetDetails/${budgetDetailId}`)
                .then((response) => {
                    // Manejar la respuesta exitosa
                    $(`#{{ $modalId }}budgetDetailsTable tbody tr[data-id="${budgetDetailId}"]`).fadeOut(
                        'slow',
                        function() {
                            // Después de que se complete el fade, remover la fila del DOM
                            $(this).remove();
                            document.dispatchEvent(new CustomEvent('budgetDetailsTable.reloadTable'));
                        });
                })
                .catch((error) => {
                    // Manejar errores
                    if (error.response) {
                        // La solicitud fue hecha y el servidor respondió con un código de estado que no está en el rango 2xx
                        console.error(error.response.data);
                        console.error(error.response.status);
                        console.error(error.response.headers);
                        // Imprimimos la respuesta del servidor en la ventana
                    } else if (error.request) {
                        // La solicitud fue hecha pero no se recibió ninguna respuesta
                        console.log("Sin respuesta");
                        console.error(error.request);
                    } else {
                        // Algo sucedió en el proceso de configuración de la solicitud que generó el error
                        console.error('Error', error.message);
                    }
                });
        });

        // Evento para recargar la tabla luego de alguna creación, eliminación o actualización
        document.addEventListener('budgetDetailsTable.reloadTable', (event) => {
            budgetDetailsTable.ajax.reload(null, false);
        });

        $(".addItemBtn").on('click', function(event) {
            addRow();
        });

        function addRow() {

            budgetDetailsTable.settings()[0].oFeatures.bServerSide = false;

            // Agregar una nueva fila al final de la tabla
            let rowNode = budgetDetailsTable.row.add({
                "id": 0,
                "item": {
                    "internalCod": "",
                    "name": "",
                },
                "unitPrice": 0.00,
                "sellPrice": 0.00,
                "quantity": 0.00,
                "taxPercentage": 0.00,
                "discount": 0.00,
            }).draw(false).node();

            budgetDetailsTable.settings()[0].oFeatures.bServerSide = true;

            $(rowNode).find("td").eq(2).html("").append(
                $('<select/>', {
                    class: "form-control select2",
                    style: "width: 100%;"
                })
            );

            $(rowNode).find(".delete-btn").remove();

            // Hacer una solicitud AJAX para obtener todos los datos
            $.ajax({
                url: `/api/budgets/${budgetId}/availableItems`,
                dataType: 'json',
                success: function(data) {
                    // Transformar los datos de la respuesta en el formato que Select2 espera
                    let select2Data = data.data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.internalCod + ' - ' + item.name
                        };
                    });

                    // Inicializar Select2 con los datos obtenidos
                    $(rowNode).find('.select2').select2({
                        dropdownParent: budgetModal,
                        data: select2Data
                    }).on('select2:select', function(e) {
                        // Cuando se selecciona un elemento, realizar una llamada al servidor para agregar una nueva fila a la base de datos
                        let itemId = e.params.data.id;
                        $.post(`/api/budgets/${budgetId}/budgetDetails`, {
                            item_id: itemId,
                            budget_id: budgetId
                        }, function() {
                            // Recargar la tabla
                            document.dispatchEvent(new CustomEvent('budgetDetailsTable.reloadTable'));
                        });
                    });
                }
            });
        }
    </script>
@endpush
