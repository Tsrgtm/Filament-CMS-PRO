<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->getSchema('form') }}
        
        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </form>
</x-filament-panels::page>
