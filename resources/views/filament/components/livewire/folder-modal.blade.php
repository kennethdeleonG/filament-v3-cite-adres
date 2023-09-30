<div>
    <style>
        /* Style the scrollbar only for this element */
        .space-y-0.overflow-y-auto::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .space-y-0.overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: #4a5568;
            border-radius: 3px;
        }

        .space-y-0.overflow-y-auto::-webkit-scrollbar-track {
            background-color: #2d3748;
            border-radius: 3px;
        }
    </style>

    {{-- edit folder modal --}}
    <x-filament::modal id="edit-folder-modal-handle" width="md" displayClasses="block">
        <form class="space-y-4" wire:submit.prevent="submit">

            {{-- tricks to disable autofocus --}}
            <input type="text" hidden autofocus />

            <x-slot name="header">
                <x-filament::modal.heading>
                    Edit Folder
                </x-filament::modal.heading>
            </x-slot>

            <div class="filament-modal-content">
                {{ $this->form }}
            </div>

            <div class="text-right flex gap-4 justify-end">
                <x-filament::button type="button" outlined color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'edit-folder-modal-handle' })">
                    Cancel
                </x-filament::button>
                <x-filament::button type="button" wire:click="editAction" wire:loading.attr="disabled">
                    <span wire:target="editAction" wire:loading.remove>
                        Save
                    </span>
                    <span wire:target="editAction" wire:loading>
                        Saving...
                    </span>
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    {{-- delete modal --}}
    <x-filament::modal id="delete-folder-modal-handle" width="sm" class="p-4 space-y-2 text-center dark:text-white"
        displayClasses="block">

        {{-- tricks to disable autofocus --}}
        <input type="text" hidden autofocus />

        <div class="text-center">
            <x-filament::modal.heading class="mb-4">
                Delete Folder
            </x-filament::modal.heading>
            <x-filament::modal.description class="mt-4">
                Are you sure you would like to do this?
            </x-filament::modal.description>
        </div>

        <div class="text-right flex gap-2 justify-end">
            <x-filament::button type="button" class="w-full" outlined color="gray"
                x-on:click="$dispatch('close-modal', { id: 'delete-folder-modal-handle' })">
                Cancel
            </x-filament::button>
            <x-filament::button type="button" class="w-full" wire:click="deleteAction" color="danger"
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
    <x-filament::modal id="move-folder-modal-handle" width="lg" displayClasses="block">
        {{-- tricks to disable autofocus --}}
        <input type="text" hidden autofocus />
        <x-slot name="header">
            <div>
                <x-filament::modal.heading>
                    Move {{ $folderName }} to:
                </x-filament::modal.heading>
                <div class="mb-2"> </div>
                <x-filament::modal.description>
                    <div class="flex justify-start items-center">
                        @if ($this->navigateRoot)
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
                @if ($folderMove->id == $folderId)
                    <div> </div>
                @else
                    <div wire:key='{{ $folderMove->id }}'
                        class="flex justify-between hover:bg-gray-500/5 dark:hover:bg-gray-300/5 p-2 rounded-md"
                        wire:loading.class.delay="opacity-50 pointer-events-none" x-data="{ showChild: false }"
                        x-on:mouseenter="showChild = true" x-on:mouseleave="showChild = false">
                        <div class="flex justify-start items-center space-x-2">
                            <x-dynamic-component class="h-6 " component="heroicon-s-folder" />
                            <div class="pl-2"> {{ $folderMove->name }}</div>
                        </div>
                        <div x-show="showChild">
                            <x-heroicon-o-arrow-right class="h-5 cursor-pointer"
                                wire:click="navigateMove({{ $folderMove->id }})" />
                        </div>
                    </div>
                @endif
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
            <x-filament::button type="button" wire:loading.attr="disabled"
                wire:click="moveFolder({{ $this->navigateFolderId }})">
                <span wire:loading.remove wire:target="moveFolder">
                    Move Here
                </span>
                <span wire:loading wire:target="moveFolder">
                    Moving...
                </span>
            </x-filament::button>
        </div>

    </x-filament::modal>

    <script>
        window.addEventListener('close-modal', event => {
            if (event.detail.id === "move-folder-modal-handle") {
                Livewire.emit('closeMoveModal');
            }
        })
    </script>
</div>
