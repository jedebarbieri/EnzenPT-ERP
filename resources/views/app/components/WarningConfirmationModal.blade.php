<!-- resources/views/app/components/ItemModal.blade.php -->

<div class="modal fade" id="{{$modalId}}" tabindex="-1" role="dialog" aria-labelledby="{{$modalId}}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="{{$modalId}}Label">
                    <i class="fas fa-exclamation-circle text-danger mr-2"></i> Confirm Delete
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do you really want to remove this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger confirmDeleteBtn" id="{{$modalId}}ConfirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
