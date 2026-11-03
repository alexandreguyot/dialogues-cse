<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Par page :
            <select wire:model.live="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('role_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="deleteSelected" wire:confirm="{{ trans('global.areYouSure') }}" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ 'Supprimer la sélection' }}
                </button>
            @endcan

            @if(file_exists(app_path('Livewire/ExcelExport.php')))
                <livewire:excel-export model="Role" format="csv" />
                <livewire:excel-export model="Role" format="xlsx" />
                <livewire:excel-export model="Role" format="pdf" />
            @endif




        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
            Recherche :
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full sm:w-1/3 inline-block" />
        </div>
    </div>
    <div wire:loading.delay>
        Chargement…
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.role.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.role.fields.title') }}
                            @include('components.table.sort', ['field' => 'title'])
                        </th>
                        <th>
                            {{ trans('cruds.role.fields.permissions') }}
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $role->id }}" wire:model.live="selected">
                            </td>
                            <td>
                                {{ $role->id }}
                            </td>
                            <td>
                                {{ $role->title }}
                            </td>
                            <td>
                                @foreach($role->permissions as $key => $entry)
                                    <span class="badge badge-relationship">{{ $entry->title }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('role_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.roles.show', $role) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('role_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.roles.edit', $role) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('role_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="delete({{ $role->id }})" wire:confirm="{{ trans('global.areYouSure') }}" wire:loading.attr="disabled">
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">Aucun résultat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-body">
        <div class="pt-3">
            @if($this->selectedCount)
                <p class="text-sm leading-5">
                    <span class="font-medium">
                        {{ $this->selectedCount }}
                    </span>
                    {{ 'sélectionné(s)' }}
                </p>
            @endif
            {{ $roles->links() }}
        </div>
    </div>
</div>