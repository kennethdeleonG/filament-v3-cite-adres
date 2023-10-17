<x-filament-panels::page>

    @if (count($this->announcementList) == 0)
        <div x-data="table" class="fi-ta" data-has-alpine-state="true" x-ignore="">
            <div
                class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
                <div
                    class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10 !border-t-0">

                    <div class="fi-ta-empty-state px-6 py-12">
                        <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                            <div
                                class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </div>

                            <h4
                                class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                No announcements
                            </h4>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @foreach ($this->announcementList as $announcement)
        <x-filament::section>
            <div class="flex items-center gap-x-3">

                <div class="flex-1">
                    <h2 class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $announcement->title }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->dateFrom($announcement->created_at) }}
                    </p>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                        {{ $announcement->content }}
                    </p>
                </div>

            </div>
        </x-filament::section>
    @endforeach


</x-filament-panels::page>
