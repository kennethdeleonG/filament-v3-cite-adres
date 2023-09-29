<x-filament::page>

    <x-filament-panels::header :actions="$this->getCachedHeaderActions()" :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []" :heading="$this->headerTitle" :subheading="$this->getSubheading()" />

    {{ $this->table }}
</x-filament::page>
