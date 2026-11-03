@php
    $id = str_replace('.', '-', $path);
    $required = $field['required'] ?? false;
    $value = data_get($this, $path);
@endphp
<div class="form-group {{ $errors->has($path) ? 'invalid' : '' }}">
    @if($field['type'] === 'image' || $field['type'] === 'icon')
        <span class="form-label">{{ $field['label'] }}</span>
        <div class="media-field">
            <div class="media-thumb">
                @if($field['type'] === 'icon')
                    {!! \App\Support\PageIcons::svg($value) ?? \App\Support\PageIcons::svg('etoile', 'icone is-placeholder') !!}
                @elseif($imageUrl = \App\Support\PageBlocks::imageUrl($value))
                    <img src="{{ $imageUrl }}" alt="">
                @else
                    <x-icone name="galerie" class="icone is-placeholder" />
                @endif
            </div>
            <div class="media-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="{{ $id }}" x-on:click="$dispatch('pick-media', { mode: '{{ $field['type'] }}', path: '{{ $path }}' })">
                    {{ $field['type'] === 'icon' ? trans('cruds.page.choose_icon') : trans('cruds.page.choose_image') }}
                </button>
                @if($field['type'] === 'image')
                    <label class="btn btn-secondary btn-sm media-upload">
                        {{ trans('cruds.page.upload_image') }}
                        <input class="sr-only" type="file" accept="image/jpeg,image/png,image/webp,image/gif" wire:model.live="uploads.{{ $id }}">
                    </label>
                @endif
                @if(filled($value))
                    <button type="button" class="btn-link" wire:click="$set('{{ $path }}', '')">{{ trans('cruds.page.remove') }}</button>
                @endif
                <div class="media-loading" wire:loading wire:target="uploads.{{ $id }}">{{ trans('cruds.page.uploading') }}</div>
            </div>
        </div>
    @else
        <label class="form-label {{ $required ? 'required' : '' }}" for="{{ $id }}">{{ $field['label'] }}</label>
        @if($field['type'] === 'textarea')
            <textarea class="form-control" id="{{ $id }}" rows="3" wire:model="{{ $path }}"></textarea>
        @else
            <input class="form-control" type="text" id="{{ $id }}" wire:model="{{ $path }}" @if($field['type'] === 'url') placeholder="/demander-un-devis ou https://…" @endif>
        @endif
    @endif

    <div class="validation-message">{{ $errors->first($path) }}</div>
</div>
