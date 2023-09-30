<x-filament-panels::page>
    <form wire:submit="create">
        {{ $this->form }}

        <br />
        <div class="text-right flex gap-4 justify-end">
            <x-filament::button type="button" outlined color="gray">
                Cancel
            </x-filament::button>
            <x-filament::button type="submit">
                Save
            </x-filament::button>
        </div>

    </form>

</x-filament-panels::page>
