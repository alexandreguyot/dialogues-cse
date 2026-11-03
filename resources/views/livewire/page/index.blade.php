<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Par page :
            <select wire:model.live="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
            Recherche :
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full sm:w-1/3 inline-block" />
        </div>
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.page.fields.title') }}
                            @include('components.table.sort', ['field' => 'title'])
                        </th>
                        <th>
                            {{ trans('cruds.page.fields.slug') }}
                            @include('components.table.sort', ['field' => 'slug'])
                        </th>
                        <th>
                            {{ trans('cruds.page.fields.is_published') }}
                            @include('components.table.sort', ['field' => 'is_published'])
                        </th>
                        <th>
                            {{ trans('cruds.page.fields.updated_at') }}
                            @include('components.table.sort', ['field' => 'updated_at'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr wire:key="page-{{ $page->id }}">
                            <td>
                                @can('page_edit')
                                    <a class="table-title" href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }}</a>
                                @else
                                    {{ $page->title }}
                                @endcan
                            </td>
                            <td>
                                @if($page->is_published)
                                    <a class="link-light-blue" href="{{ route('pages.show', $page->slug) }}" target="_blank">/{{ $page->slug }}</a>
                                @else
                                    /{{ $page->slug }}
                                @endif
                            </td>
                            <td>
                                <span class="status {{ $page->is_published ? 'is-online' : '' }}">{{ $page->is_published ? trans('cruds.page.published') : trans('cruds.page.draft') }}</span>
                            </td>
                            <td>
                                {{ $page->updated_at?->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('page_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.pages.show', $page) }}" target="_blank">
                                            {{ trans('cruds.page.preview') }}
                                        </a>
                                    @endcan
                                    @can('page_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.pages.edit', $page) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('page_create')
                                        <button class="btn btn-sm btn-secondary mr-2" type="button" wire:click="duplicate({{ $page->id }})" wire:loading.attr="disabled">
                                            {{ trans('cruds.page.duplicate') }}
                                        </button>
                                    @endcan
                                    @can('page_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="delete({{ $page->id }})" wire:confirm="{{ trans('global.areYouSure') }}" wire:loading.attr="disabled">
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty">{{ trans('cruds.page.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-body">
        <div class="pt-3">
            {{ $pages->links() }}
        </div>
    </div>
</div>
