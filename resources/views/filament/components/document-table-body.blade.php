@props(['actions', 'document', 'type'])

<tr
    x-bind:class="{
        'hidden': false & amp; & amp;isGroupCollapsed(''),
        'bg-gray-50 dark:bg-white/5': isRecordSelected('1'),
        '[&amp;>*:first-child]:relative [&amp;>*:first-child]:before:absolute [&amp;>*:first-child]:before:start-0 [&amp;>*:first-child]:before:inset-y-0 [&amp;>*:first-child]:before:w-0.5 [&amp;>*:first-child]:before:bg-primary-600 [&amp;>*:first-child]:dark:before:bg-primary-500': isRecordSelected(
            '1'),
    }">

    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div>
                                <div style="width: 70%; display: flex; justify-content: flex-start">
                                    @if ($type == 'folder')
                                        <img src="{{ asset('images/icons/folder.svg') }}" alt=""
                                            style="width: 25px; margin-right: 7px;" />
                                    @else
                                        <img src="{{ asset("images/icons/{$this->getAssetIconImage($document)}") }}"
                                            alt="" style="width: 25px; margin-right: 7px;" />
                                    @endif
                                    <span>{{ $document->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div>
                                @if ($type == 'folder')
                                    {{ $this->getFolderSize($document->id) }}
                                @else
                                    {{ $this->convertedAssetSize($asset->size ?: 0) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div>
                                @if ($type == 'folder')
                                    Folder
                                @else
                                    {{ $document->file_type }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div>
                                {{ $this->convertedDate($document->updated_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div>
                                {{ $document->is_private ? 'Private' : 'Public' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name"
        wire:key="ulem3AZdbQ9IWh0qx1MP.table.record.1.column.name">
        <div class="fi-ta-col-wrp">
            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                <div class="">
                    <div class="flex max-w-max">
                        <div
                            class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
                            <div class="fi-ac gap-3 flex flex-wrap items-center justify-start shrink-0">
                                {{-- ACTIONS --}}
                                <div x-data="{
                                    toggle: function(event) {
                                        $refs.panel.toggle(event)
                                    },
                                
                                    open: function(event) {
                                        $refs.panel.open(event)
                                    },
                                
                                    close: function(event) {
                                        $refs.panel.close(event)
                                    },
                                }" class="fi-dropdown">
                                    <div x-on:click="toggle" class="fi-dropdown-trigger flex cursor-pointer"
                                        aria-expanded="true">
                                        <button style=""
                                            class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75  disabled:pointer-events-none disabled:opacity-70 h-9 w-9 fi-color-custom text-custom-500 hover:text-custom-600 focus:ring-custom-600 dark:text-custom-400 dark:hover:text-custom-300 dark:focus:ring-custom-500 fi-ac-icon-btn-group"
                                            type="button">

                                            <svg class="fi-icon-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z">
                                                </path>
                                            </svg>

                                        </button>
                                    </div>

                                    <div x-float.placement.bottom-start.flip.teleport.offset="{ offset: 8 }"
                                        x-ref="panel" x-transition:enter-start="opacity-0"
                                        x-transition:leave-end="opacity-0"
                                        class="fi-dropdown-panel absolute z-10 w-screen divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:divide-white/5 dark:bg-gray-900 dark:ring-white/10 max-w-[14rem]"
                                        style="position: fixed; display: block; left: 1024px; top: 140px;">
                                        <div class="fi-dropdown-list p-1">
                                            @if ($type == 'folder')
                                                @foreach ($actions as $action)
                                                    <button style=";"
                                                        class="fi-dropdown-list-item flex w-full items-center gap-2 whitespace-nowrap
                                                         rounded-md p-2 text-sm transition-colors duration-75
                                                          outline-none disabled:pointer-events-none disabled:opacity-70 
                                                          fi-color-gray fi-dropdown-list-item-color-gray hover:bg-gray-50
                                                           focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5 fi-ac-grouped-action"
                                                        type="button" wire:loading.attr="disabled"
                                                        wire:target="mountActionFolder('{{ $action['action'] }}', {{ $document['id'] }})"
                                                        wire:click="mountActionFolder('{{ $action['action'] }}', {{ $document['id'] }})">
                                                        <svg fill="none" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="animate-spin fi-dropdown-list-item-icon h-5 w-5 text-gray-400 dark:text-gray-500"
                                                            wire:loading.delay=""
                                                            wire:target="mountActionFolder('{{ $action['action'] }}', {{ $document['id'] }})">
                                                            <path clip-rule="evenodd"
                                                                d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                                fill-rule="evenodd" fill="currentColor" opacity="0.2">
                                                            </path>
                                                            <path
                                                                d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                                                                fill="currentColor"></path>
                                                        </svg>

                                                        <span
                                                            class="fi-dropdown-list-item-label flex-1 truncate text-start text-gray-700 dark:text-gray-200">
                                                            {{ $action['label'] }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            @else
                                                @foreach ($actions as $action)
                                                    <button style=";"
                                                        class="fi-dropdown-list-item flex w-full items-center gap-2 whitespace-nowrap
                                                 rounded-md p-2 text-sm transition-colors duration-75
                                                  outline-none disabled:pointer-events-none disabled:opacity-70 
                                                  fi-color-gray fi-dropdown-list-item-color-gray hover:bg-gray-50
                                                   focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5 fi-ac-grouped-action"
                                                        type="button" wire:loading.attr="disabled"
                                                        wire:target="mountActionAsset('{{ $action['action'] }}', {{ $document['id'] }})"
                                                        wire:click="mountActionAsset('{{ $action['action'] }}', {{ $document['id'] }})">
                                                        <svg fill="none" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="animate-spin fi-dropdown-list-item-icon h-5 w-5 text-gray-400 dark:text-gray-500"
                                                            wire:loading.delay=""
                                                            wire:target="mountActionAsset('{{ $action['action'] }}', {{ $document['id'] }})">
                                                            <path clip-rule="evenodd"
                                                                d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                                fill-rule="evenodd" fill="currentColor"
                                                                opacity="0.2">
                                                            </path>
                                                            <path
                                                                d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                                                                fill="currentColor"></path>
                                                        </svg>

                                                        <span
                                                            class="fi-dropdown-list-item-label flex-1 truncate text-start text-gray-700 dark:text-gray-200">
                                                            {{ $action['label'] }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
