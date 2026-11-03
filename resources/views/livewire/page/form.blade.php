<form wire:submit="submit" x-data class="editor">
    <div class="editor-main">
        <div class="form-group editor-title {{ $errors->has('title') ? 'invalid' : '' }}">
            <label class="sr-only" for="title">{{ trans('cruds.page.fields.title') }}</label>
            <input type="text" id="title" wire:model.live.blur="title" placeholder="{{ trans('cruds.page.title_placeholder') }}" autocomplete="off">
            <div class="validation-message">{{ $errors->first('title') }}</div>
        </div>

        @if($errors->has('blocks'))
            <p class="editor-error">{{ $errors->first('blocks') }}</p>
        @endif

        @foreach($blocks as $i => $block)
            @php
                $definition = $blockTypes[$block['type']];
                $hasError = collect($errors->keys())->contains(fn ($key) => str_starts_with($key, "blocks.$i."));
            @endphp
            {{-- La clé change en cas d'erreur : le bloc se rouvre tout seul. --}}
            <section class="block" wire:key="block-{{ $block['id'] }}-{{ $hasError ? 'error' : 'ok' }}" x-data="{ open: true }">
                <header class="block-header">
                    <button type="button" class="block-toggle" x-on:click="open = ! open" x-bind:aria-expanded="open" title="{{ trans('cruds.page.collapse') }}">
                        <x-icone name="{{ $definition['icon'] }}" />
                        <span>{{ $definition['label'] }}</span>
                        <span class="block-chevron" x-bind:class="open || 'is-closed'"><x-icone name="chevron-bas" /></span>
                    </button>
                    <div class="block-tools">
                        <button type="button" class="tool" aria-label="{{ trans('cruds.page.move_up') }}" title="{{ trans('cruds.page.move_up') }}" wire:click="moveBlock({{ $i }}, -1)" @disabled($loop->first)><x-icone name="fleche-haut" /></button>
                        <button type="button" class="tool" aria-label="{{ trans('cruds.page.move_down') }}" title="{{ trans('cruds.page.move_down') }}" wire:click="moveBlock({{ $i }}, 1)" @disabled($loop->last)><x-icone name="fleche-bas" /></button>
                        <button type="button" class="tool is-danger" aria-label="{{ trans('global.delete') }}" title="{{ trans('global.delete') }}" wire:click="removeBlock({{ $i }})" wire:confirm="{{ trans('cruds.page.remove_block_confirm') }}"><x-icone name="supprimer" /></button>
                    </div>
                </header>

                <div class="block-body" x-show="open">
                    @foreach($definition['fields'] ?? [] as $name => $field)
                        @include('livewire.page.field', ['path' => "blocks.$i.$name", 'field' => $field])
                    @endforeach

                    @isset($definition['items'])
                        @if($errors->has("blocks.$i.items"))
                            <p class="editor-error">{{ $errors->first("blocks.$i.items") }}</p>
                        @endif
                        <div class="items">
                            @foreach($block['items'] as $j => $item)
                                <div class="item" wire:key="item-{{ $item['id'] }}">
                                    <div class="item-header">
                                        <span>{{ $definition['items']['label'] }} {{ $loop->iteration }}</span>
                                        <div class="block-tools">
                                            <button type="button" class="tool" aria-label="{{ trans('cruds.page.move_up') }}" title="{{ trans('cruds.page.move_up') }}" wire:click="moveItem({{ $i }}, {{ $j }}, -1)" @disabled($loop->first)><x-icone name="fleche-gauche" /></button>
                                            <button type="button" class="tool" aria-label="{{ trans('cruds.page.move_down') }}" title="{{ trans('cruds.page.move_down') }}" wire:click="moveItem({{ $i }}, {{ $j }}, 1)" @disabled($loop->last)><x-icone name="fleche-droite" /></button>
                                            <button type="button" class="tool is-danger" aria-label="{{ trans('global.delete') }}" title="{{ trans('global.delete') }}" wire:click="removeItem({{ $i }}, {{ $j }})" @disabled($loop->count <= $definition['items']['min'])><x-icone name="supprimer" /></button>
                                        </div>
                                    </div>
                                    @foreach($definition['items']['fields'] as $name => $field)
                                        @include('livewire.page.field', ['path' => "blocks.$i.items.$j.$name", 'field' => $field])
                                    @endforeach
                                </div>
                            @endforeach
                            @if(count($block['items']) < $definition['items']['max'])
                                <button type="button" class="item-add" wire:click="addItem({{ $i }})">
                                    <x-icone name="ajouter" />
                                    {{ $definition['items']['label'] }}
                                </button>
                            @endif
                        </div>
                    @endisset
                </div>
            </section>
        @endforeach

        <section class="palette">
            <h2>{{ trans('cruds.page.add_block') }}</h2>
            <div class="palette-list">
                @foreach($blockTypes as $type => $definition)
                    <button type="button" wire:click="addBlock('{{ $type }}')">
                        <x-icone name="{{ $definition['icon'] }}" />
                        <strong>{{ $definition['label'] }}</strong>
                        <span>{{ $definition['help'] }}</span>
                    </button>
                @endforeach
            </div>
        </section>
    </div>

    <aside class="editor-side">
        <div class="editor-side-inner">
            <label class="switch">
                <input type="checkbox" wire:model="isPublished">
                <span class="switch-track"></span>
                {{ trans('cruds.page.fields.is_published_helper') }}
            </label>
            <label class="switch">
                <input type="checkbox" wire:model="inMenu">
                <span class="switch-track"></span>
                {{ trans('cruds.page.fields.in_menu_helper') }}
            </label>

            <div class="form-group {{ $errors->has('slug') ? 'invalid' : '' }}">
                <label class="form-label required" for="slug">{{ trans('cruds.page.fields.slug') }}</label>
                <input class="form-control" type="text" id="slug" wire:model.live.blur="slug">
                <div class="validation-message">{{ $errors->first('slug') }}</div>
                <div class="help-block">{{ url('/') }}/{{ $slug ?: '…' }}</div>
            </div>

            <fieldset class="form-group">
                <legend class="form-label">{{ trans('cruds.page.fields.theme') }}</legend>
                <div class="theme-choice">
                    @foreach(['light', 'dark'] as $themeOption)
                        <label class="theme-{{ $themeOption }}">
                            <input type="radio" value="{{ $themeOption }}" wire:model="theme">
                            <span>{{ trans('cruds.page.themes.' . $themeOption) }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="form-group {{ $errors->has('metaDescription') ? 'invalid' : '' }}">
                <label class="form-label" for="metaDescription">{{ trans('cruds.page.fields.meta_description') }}</label>
                <textarea class="form-control" id="metaDescription" rows="3" wire:model="metaDescription"></textarea>
                <div class="validation-message">{{ $errors->first('metaDescription') }}</div>
                <div class="help-block">{{ trans('cruds.page.fields.meta_description_helper') }}</div>
            </div>

            @if($errors->any())
                <p class="editor-error" role="alert">{{ trans('cruds.page.has_errors') }}</p>
            @endif

            <div class="editor-actions">
                <button class="btn btn-indigo" type="submit" wire:loading.attr="disabled">{{ trans('global.save') }}</button>
                <button class="btn btn-secondary" type="button" wire:click="preview" wire:loading.attr="disabled">{{ trans('cruds.page.preview') }}</button>
            </div>
            <a class="editor-back" href="{{ route('admin.pages.index') }}">Retour à la liste des pages</a>
        </div>
    </aside>

    <div class="modal" wire:ignore x-data="mediaPicker" x-show="open" x-cloak x-on:pick-media.window="show($event.detail)" x-on:keydown.escape.window="open && close()" role="dialog" aria-modal="true">
        <div class="modal-panel">
            <header class="modal-header">
                <input class="form-control" type="search" x-ref="search" x-model="query" placeholder="{{ trans('cruds.page.search') }}" aria-label="{{ trans('cruds.page.search') }}">
                <button type="button" class="tool" aria-label="{{ trans('cruds.page.close') }}" x-on:click="close()"><x-icone name="fermer" /></button>
            </header>
            <div class="picker-themes">
                <button type="button" x-bind:class="theme === '' && 'is-active'" x-on:click="theme = ''">{{ trans('cruds.page.all_themes') }}</button>
                <template x-for="name in themes" x-bind:key="name">
                    <button type="button" x-text="name" x-bind:class="theme === name && 'is-active'" x-on:click="theme = name"></button>
                </template>
            </div>
            <div class="picker-grid" x-bind:class="'is-' + mode">
                <template x-for="item in filtered" x-bind:key="item.value">
                    <button type="button" x-on:click="choose(item)">
                        <template x-if="item.svg"><span class="picker-preview" x-html="item.svg"></span></template>
                        <template x-if="item.url"><img class="picker-preview" x-bind:src="item.url" alt="" loading="lazy"></template>
                        <span x-text="item.label"></span>
                    </button>
                </template>
                <p class="picker-empty" x-show="filtered.length === 0">{{ trans('cruds.page.no_result') }}</p>
            </div>
        </div>
    </div>

    <div class="modal" wire:ignore x-data="{ open: false, mobile: false }" x-show="open" x-cloak x-on:page-preview.window="$refs.frame.srcdoc = $event.detail.html; open = true" x-on:keydown.escape.window="open = false" role="dialog" aria-modal="true" aria-label="{{ trans('cruds.page.preview') }}">
        <div class="modal-panel preview-panel">
            <header class="modal-header">
                <div class="picker-themes">
                    <button type="button" x-bind:class="! mobile && 'is-active'" x-on:click="mobile = false">{{ trans('cruds.page.preview_desktop') }}</button>
                    <button type="button" x-bind:class="mobile && 'is-active'" x-on:click="mobile = true">{{ trans('cruds.page.preview_mobile') }}</button>
                </div>
                <button type="button" class="tool" aria-label="{{ trans('cruds.page.close') }}" x-on:click="open = false"><x-icone name="fermer" /></button>
            </header>
            <iframe x-ref="frame" title="{{ trans('cruds.page.preview') }}" x-bind:class="mobile && 'is-mobile'"></iframe>
        </div>
    </div>
</form>
