@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 justify-content-between">
                    <div class="col-sm-6">
                        <h1 class="m-0">Budgets</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#budgetDetailsModal">
                            <i class="fa fa-plus"></i> New Budget
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
                            <div class="card-body">
                                <table id="budgetsTable" class="table table-hover"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('app.components.BudgetModal', [
        'modalId' => 'budgetDetailsModal'
    ])
    @include('app.components.WarningConfirmationModal', [
        'modalId' => 'budgetDeleteConfirmationModal',
        'message' => 'Do you really want to remove this budget?',
    ])
@endsection

@push('page_scripts')
    <template id="buttonsOptionsPerLineTemplate">
        <td class="text-right text-nowrap" style="width: 0%">
            <div class="button-container">
                <button class="btn btn-success btn-sm view-btn" data-id="__ID__" title="PDF Download"><i
                        class="fas fa-file-pdf"></i></button>
                <button class="btn btn-primary btn-sm edit-btn" data-id="__ID__" title="Edit"><i
                        class="fas fa-edit"></i></button>
                <button class="btn btn-outline-danger btn-sm delete-btn" data-id="__ID__" title="Delete"><i
                        class="fas fa-trash"></i></button>
            </div>
        </td>
    </template>
    <script type="module">
        var budgetsTable = $('#budgetsTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,

            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [20, 100, 200],

            "order": [[2, 'asc'], [3, 'desc']],

            "ajax": {
                "url": "/api/budgets",
                "type": "GET",
                "dataSrc": function(json) {
                    json.recordsFiltered = json.metadata.recordsFiltered;
                    json.recordsTotal = json.metadata.recordsTotal;
                    return json.data.budgetList;
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
                    "data": "status",
                    "title": "Status",
                    "width": "0%",
                    "class": "text-nowrap",
                    "render": function(data, type, row) {
                        let color;
                        if (data.value === 0) {
                            color = "secondary";
                        } else if (data.value === 1) {
                            color = "success";
                        } else if (data.value === 2) {
                            color = "danger";
                        } else {
                            color = "secondary";
                        }
                        return $('<span/>', {
                            "class": `badge badge-${color}`,
                            "text": data.display
                        }).prop("outerHTML");
                    }
                },
                {
                    "data": "updatedAt",
                    "title": "Last Update",
                    "width": "0%",
                    "class": "text-nowrap text-right",
                    "render": function(data, type, row) {
                        let date = new Date(data);
                        return date.toLocaleString('pt-PT', window.shortDateFormat);
                    }
                }
            ],
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
                let clonedContent = $("#buttonsOptionsPerLineTemplate").contents().clone();

                // Reemplazar los placeholders con valores dinámicos
                clonedContent.find('.delete-btn').attr('data-id', data.id);
                clonedContent.find('.edit-btn').attr('data-id', data.id);

                // Añade la clase deseada a la fila
                $(row).prepend(clonedContent);

                // Añadimos atributo de data-id a cada fila
                $(row).attr("data-id", data.id);
            }
        });

        $('#budgetsTable').on('click', '.delete-btn', function(event) {
            var budgetId = $(this).data('id');
            // Mostrar cuadro de diálogo para confirmar eliminación
            $("#budgetDeleteConfirmationModal").modal("show");


            // Configurar el evento clic para el botón de confirmación dentro del modal
            $('#budgetDeleteConfirmationModalConfirmDeleteBtn').off('click').on('click', () => {
                // Aquí puedes realizar la lógica de eliminación
                axios.delete(`api/budgets/${budgetId}`)
                    .then((response) => {
                        // Manejar la respuesta exitosa
                        $(`#budgetsTable tbody tr[data-id="${budgetId}"]`).fadeOut('slow', function() {
                            // Después de que se complete el fade, remover la fila del DOM
                            $(this).remove();
                            document.dispatchEvent(new CustomEvent('budgetsTable.reloadTable'));
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
                    })
                    .finally(() => {
                        // Cerrar el modal después de la eliminación
                        $('#budgetDeleteConfirmationModal').modal('hide');
                    });
            });
        });

        $('#budgetsTable').on('click', '.edit-btn', function(event) {
            var budgetId = $(this).data('id');

            let row = budgetsTable.rows(`[data-id="${budgetId}"]`);

            // Verificar si la fila existe
            if (!row.any()) {
                console.error('La fila con data-id', dataId, 'no fue encontrada en budgetsTable.');
                return;
            }

            // Obtener el objeto de datos asociado a la fila
            var rowData = row.data()[0];

            document.dispatchEvent(new CustomEvent('budgetModal.loadData', {
                detail: {
                    data: rowData
                }
            }));
        });

        // Evento para recargar la tabla luego de alguna creación, eliminación o actualización
        document.addEventListener('budgetsTable.reloadTable', (event) => {
            budgetsTable.ajax.reload(null, false);
        });
    </script>
@endpush
