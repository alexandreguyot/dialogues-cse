<div>
    <div wire:ignore class="w-full">
        @if(isset($attributes['multiple']))
            <div id="{{ $attributes['id'] }}-btn-container" class="mb-3">
                <button type="button" class="btn btn-info btn-sm select-all-button">{{ trans('global.select_all') }}</button>
                <button type="button" class="btn btn-info btn-sm deselect-all-button">{{ trans('global.deselect_all') }}</button>
            </div>
        @endif
        <select class="select2 form-control" data-placeholder="{{ 'Sélectionner' }}" {{ $attributes }}>
            @if(!isset($attributes['multiple']))
                <option></option>
            @endif
            @foreach($options as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("livewire:initialized", () => {
    let el = $('#{{ $attributes['id'] }}')
    let buttonsId = '#{{ $attributes['id'] }}-btn-container'

    // select2 ne déclenche que des événements jQuery : wire:model écoute l'événement natif.
    function syncLivewire() {
        el[0].dispatchEvent(new Event('change', { bubbles: true }))
    }

    $(buttonsId + ' .select-all-button').click(function (e) {
        el.val(el.find('option').map((i, opt) => $(opt).attr('value')).get())
        el.trigger('change.select2')
        syncLivewire()
    })

    $(buttonsId + ' .deselect-all-button').click(function (e) {
        el.val([])
        el.trigger('change.select2')
        syncLivewire()
    })

    el.select2({
        placeholder: '{{ 'Sélectionner' }}',
        allowClear: !el.attr('required')
    })

    el.on('select2:select select2:unselect select2:clear', syncLivewire)
});
    </script>
@endpush
