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
                                <table id="budgetsTable" class="table table-bordered table-hover"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    {{-- @include('app.components.BudgetModal', [
        'modalId' => 'budgetDetailsModal'
    ]) --}}
    @include('app.components.WarningConfirmationModal', [
        'modalId' => 'budgetDeleteConfirmationModal',
        'message' => 'Do you really want to remove this budget?'
    ])
@endsection

@push('page_scripts')
    <template id="buttonsOptionsPerLineTemplate">
        <td class="text-right text-nowrap" style="width: 0%">
            <div class="button-container">
                <button class="btn btn-primary btn-sm edit-btn" data-id="__ID__" title="Edit"><i
                        class="fas fa-edit"></i></button>
                <button class="btn btn-outline-danger btn-sm delete-btn" data-id="__ID__" title="Delete"><i
                        class="fas fa-trash"></i></button>
            </div>
        </td>
    </template>
    <script type="module">
        var budgetsTable = $('#projectsTable').DataTable({
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

            "ajax": {
                "url": "/api/budgets",
                "type": "GET",
                "dataSrc": function(json) {
                    json.recordsFiltered = json.metadata.recordsFiltered;
                    json.recordsTotal = json.metadata.recordsTotal;
                    return json.data;
                }
            },
            "columns": [
                // {
                //     "data": "id",
                //     "title": "Id",
                //     "visible": false // Oculta la columna "Id"
                // },
                // {
                //     "data": "internalCod",
                //     "title": "Internal Code",
                //     "width": "0%",
                //     "class": "text-nowrap"
                // },
                // {
                //     "data": "name",
                //     "title": "Name"
                // },
                // {
                //     "data": "unitPrice",
                //     "title": "Unit Price",
                //     "width": "0%",
                //     "class": "text-nowrap",
                //     "render": function(data, type, row) {
                //         // Formatear el valor como moneda en euros
                //         return new Intl.NumberFormat('pt-PT', {
                //             style: 'currency',
                //             currency: 'EUR'
                //         }).format(data);
                //     }
                // },
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
            },
            "columnDefs": [{
                "targets": [3], // Índice de la columna "Unit Price"
                "className": "text-right" // Clase de alineación a la derecha
            }, ]
        });

        $('#budgetsTable').on('click', '.delete-btn', function(event) {
            var itemId = $(this).data('id');
            // Mostrar cuadro de diálogo para confirmar eliminación
            $("#budgetDeleteConfirmationModal").modal("show");


            // Configurar el evento clic para el botón de confirmación dentro del modal
            $('#budgetDeleteConfirmationModalConfirmDeleteBtn').off('click').on('click', () => {
                // Aquí puedes realizar la lógica de eliminación
                axios.delete(`api/items/${itemId}`)
                    .then((response) => {
                        // Manejar la respuesta exitosa
                        $(`#budgetsTable tbody tr[data-id="${itemId}"]`).fadeOut('slow', function() {
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
            var itemId = $(this).data('id');
            // Llamar al servicio para obtener los detalles del elemento con el ID itemId
            // Luego, cargar los datos en la ventana modal y mostrarla
            let row = itemsTable.rows(`[data-id="${itemId}"]`);
            
            // Verificar si la fila existe
            if (!row.any()) {
                console.error('La fila con data-id', dataId, 'no fue encontrada en itemsTable.');
                return;
            }
            
            // Obtener el objeto de datos asociado a la fila
            var rowData = row.data()[0];

            document.dispatchEvent(new CustomEvent('itemModal.loadData', {
                detail: {
                    data: rowData
                }
            } ));
        });

        // Evento para recargar la tabla luego de alguna creación, eliminación o actualización
        document.addEventListener('itemsTable.reloadTable', (event) => {
            itemsTable.ajax.reload(null, false);
        });
    </script>
@endpush
