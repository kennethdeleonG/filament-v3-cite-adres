@props([
    'actions',
    'color' => null,
    'darkMode' => config('filament.dark_mode'),
    'icon' => 'heroicon-o-dots-vertical',
    'label' => __('filament-support::actions/group.trigger.label'),
    'size' => null,
    'tooltip' => null,
])

@php
    $user = Auth::user();

    $isFaculty = $user->getTable() == 'faculties';
@endphp

@if (!$isFaculty)
    <div class="flex lg:justify-between justify-end gap-2 ">
        <select wire:model.live="filterBy" style="background-position: right 0.2rem center" @class([
            'text-sm pl-2   font-medium bg-white rounded-lg border-gray-300 border-[1px] sm:text-sm focus:ring-1 focus:border-primary-300 focus:ring-primary-500',
            'dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-300' => config(
                'tables.dark_mode'),
        ])>
            <option value="" selected>All</option>
            <option value="public">Public</option>
            <option value="private">Private</option>
        </select>
    </div>
@endif

<x-filament::dropdown :dark-mode="$darkMode" placement="bottom-end" teleport {{ $attributes }}>
    <x-slot name="trigger">
        <x-filament::button :color="$color" :dark-mode="$darkMode" :size="$size" :tooltip="$tooltip">
            {{ $label }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($actions as $action)
            @if (!$action->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
