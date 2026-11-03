@if(in_array($field, $orderable))
    <button type="button" class="table-sort {{ $sortBy === $field ? 'is-active' : '' }}" wire:click="sortBy('{{ $field }}')" aria-label="Trier">
        {{ $sortBy !== $field ? '↕' : ($sortDirection === 'desc' ? '↓' : '↑') }}
    </button>
@endif
