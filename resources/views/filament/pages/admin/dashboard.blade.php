<x-filament::page class="filament-dashboard-page">

    <x-filament-widgets::widgets :columns="$this->getHeaderWidgetsColumns()" :widgets="$this->getWidgets()" class="fi-page-header-widgets" />

    <x-filament::modal.heading>
        Recent ({{ count($this->assetList) }}) Files
    </x-filament::modal.heading>

    <div
        class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr class="">
                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Name
                            </span>

                        </span>
                    </th>
                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Size
                            </span>

                        </span>
                    </th>
                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Type
                            </span>

                        </span>
                    </th>
                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Date Modified
                            </span>

                        </span>
                    </th>

                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Visibility
                            </span>

                        </span>
                    </th>
                    <th class="w-5"></th>
                </tr>
            </thead>
            <tbody class="divide-y whitespace-nowrap dark:divide-gray-700">
                @foreach ($this->assetList as $asset)
                    @include('filament.components.document-table-body', [
                        'document' => $asset,
                        'type' => 'asset',
                        'actions' => $this->getAssetActions(),
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

    @livewire('asset-modal', ['folderId' => null])
</x-filament::page>
