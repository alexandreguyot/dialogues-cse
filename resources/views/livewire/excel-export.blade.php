<button wire:click="export" type="button" class="btn btn-secondary uppercase disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
    {{ $format }}
</button>