<!-- resources/views/app/components/SelectDropdown.blade.php -->

@php
function getDisplayName($displayFunc, $element) {
    if (isset($displayFunc) && is_callable($displayFunc)) {
        return call_user_func($displayFunc, $element);
    } else {
        return $element->name;
    }
}
@endphp

<div class="form-group row">
    <label class="col-sm-2" for="{{ $name }}">{{ $label }}</label>
    <div class="col-sm-10">
        <select id="{{ $id }}" name="{{ $name }}" class="form-control select2" style="width: 100%;">
            <option value="">Select...</option>
            @if (isset($elements))
                @foreach ($elements as $element)
                    @if (isset($element['children']) && count($element['children']) > 0)
                        <optgroup label="{{ getDisplayName($displayFunc, $element) }}">
                            @foreach($element['children'] as $child)
                                <option value="{{ $child['id'] }}">{{ getDisplayName($displayFunc, $child) }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $element['id'] }}">{{ $element['name'] }}</option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
</div>

@push('page_scripts')
    <script type="module">
        // This is thinked to allow the dinamic loading of this component.
    @if (isset($ajaxUrl))
        $("#{{ $id }}").select2({
            dropdownParent: $("#{{ $dropdownParent }}"),
            ajax: {
                url: "{{ $ajaxUrl }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(category) {
                            if (category.children) {
                                category.children.map((subCat) => {
                                    return {
                                        id: subCat.id,
                                        text: subCat.name
                                    };
                                });
                            }
                            return {
                                id: category.id,
                                text: category.name
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            placeholder: 'Select...',
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    @else
        $("#{{ $id }}").select2({
            dropdownParent: $("#{{ $dropdownParent }}"),
        });
    @endif
    </script>
@endpush
