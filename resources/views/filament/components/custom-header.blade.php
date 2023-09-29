<div>
    <x-filament-panels::header :actions="$this->getCachedHeaderActions()" :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []" :heading="$this->getHeading()" :subheading="$this->getSubheading()" />

    {{-- <div class="">
        @if (count($this->getBreadcrumbsMenu()) > 1)
            <x-filament::layouts.app.topbar.breadcrumbs :breadcrumbs="$this->getBreadcrumbsMenu()" />
        @endif
    </div> --}}
</div>
