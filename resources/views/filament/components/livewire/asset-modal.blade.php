<div>
    {{-- delete modal --}}
    <x-filament::modal id="delete-asset-modal-handle" width="sm" class="p-4 space-y-2 text-center dark:text-white"
        displayClasses="block">

        {{-- tricks to disable autofocus --}}
        <input type="text" hidden autofocus />

        <div class="text-center">
            <x-filament::modal.heading class="mb-4">
                Delete Document
            </x-filament::modal.heading>
            <x-filament::modal.description class="mt-4">
                Are you sure you would like to do this?
            </x-filament::modal.description>
        </div>

        <div class="text-right flex gap-2 justify-end">
            <x-filament::button type="button" class="w-full" outlined color="gray"
                x-on:click="$dispatch('close-modal', { id: 'delete-asset-modal-handle' })">
                Cancel
            </x-filament::button>
            <x-filament::button type="submit" class="w-full" wire:click="deleteAction" color="danger"
                wire:loading.attr="disabled">
                <span wire:target="deleteAction" wire:loading.remove>
                    Confirm
                </span>
                <span wire:target="deleteAction" wire:loading>
                    Confirming...
                </span>
            </x-filament::button>
        </div>
    </x-filament::modal>

    {{-- move to modal --}}
    <x-filament::modal id="move-asset-modal-handle" width="lg" displayClasses="block"
        class="p-4 space-y-2 dark:text-white">

        {{-- tricks to disable autofocus --}}
        <input type="text" hidden autofocus />

        <x-slot name="header">
            <div>
                <x-filament::modal.heading>
                    Move {{ $assetName }} to:
                </x-filament::modal.heading>
                <div class="mb-2"> </div>
                <x-filament::modal.description>
                    <div class="flex justify-start items-center">
                        @if ($this->navigateRoot)
                            <div class="mr-2" wire:click="navigateMove({{ $this->previousFolderId }})">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 cursor-pointer"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </div>
                            <div class="mr-2">
                                <x-heroicon-o-arrow-left class="h-5 cursor-pointer"
                                    wire:click="navigateMove({{ $this->previousFolderId }})" />
                            </div>
                        @endif
                        <div>
                            {{ $this->navigateFolderName }}
                        </div>
                    </div>
                </x-filament::modal.description>
            </div>
        </x-slot>

        <div class="space-y-0 overflow-y-auto " style="height: 250px;">
            @foreach ($this->getMoveFolders() as $folderMove)
                <div wire:key='{{ $folderMove->id }}'
                    class="flex justify-between hover:bg-gray-500/5 dark:hover:bg-gray-300/5 p-2 rounded-md"
                    wire:loading.class.delay="opacity-50 pointer-events-none" x-data="{ showChild: false }"
                    x-on:mouseenter="showChild = true" x-on:mouseleave="showChild = false">
                    <div class="flex justify-start items-center">
                        <x-dynamic-component class="h-6" component="heroicon-s-folder" />
                        <div class="pl-2"> {{ $folderMove->name }}</div>
                    </div>
                    <div x-show="showChild">
                        <x-heroicon-o-arrow-right class="h-5 cursor-pointer"
                            wire:click="navigateMove({{ $folderMove->id }})" />
                    </div>
                </div>
            @endforeach
            @if (count($this->getMoveFolders()) == 0)
                <div>
                    <div class="w-full flex items-center justify-center" style="height: 150px">
                        <p> This folder is empty. </p>
                    </div>
                </div>
            @endif
        </div>

        <div wire:loading.remove wire:target="navigateMove" class="text-right flex gap-2 justify-end">
            <x-filament::button type="submit" wire:loading.attr="disabled"
                wire:click="moveAsset({{ $this->navigateFolderId }})">
                <span wire:loading.remove wire:target="moveAsset">
                    Move Here
                </span>
                <span wire:loading wire:target="moveAsset">
                    Moving...
                </span>
            </x-filament::button>
        </div>
    </x-filament::modal>

    <script>
        window.addEventListener('close-modal', event => {
            if (event.detail.id === "move-asset-modal-handle") {
                Livewire.emit('closeMoveModalAsset');
            }
        })
    </script>
</div>
