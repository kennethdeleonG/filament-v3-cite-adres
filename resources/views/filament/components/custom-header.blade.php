<div>
    <x-filament-panels::header :actions="$this->getCachedHeaderActions()" :breadcrumbs="count($this->getBreadcrumbsMenu()) > 1 ? $this->getBreadcrumbsMenu() : []" :heading="$this->getHeading()" :subheading="$this->getSubheading()" />

    {{-- <div class="">
        @if (count($this->getBreadcrumbsMenu()) > 1)
            <x-filament::layouts.app.topbar.breadcrumbs :breadcrumbs="$this->getBreadcrumbsMenu()" />
        @endif
    </div> --}}
</div>
