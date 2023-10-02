<x-filament-panels::page>
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
                                Location
                            </span>

                        </span>
                    </th>

                    <th
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-designation">
                        <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">

                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                Deleted At
                            </span>

                        </span>
                    </th>
                    <th class="w-5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                @foreach ($this->folderList as $folder)
                    @include('filament.components.document-table-body-trashed', [
                        'document' => $folder,
                        'type' => 'folder',
                        'actions' => $this->getFolderActions(),
                    ])
                @endforeach
                @foreach ($this->assetList as $asset)
                    @include('filament.components.document-table-body-trashed', [
                        'document' => $asset,
                        'type' => 'asset',
                        'actions' => $this->getAssetActions(),
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

</x-filament-panels::page>
