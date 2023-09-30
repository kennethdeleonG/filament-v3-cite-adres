<?php

namespace App\Providers\Filament;

use App\Filament\Faculty\Pages\CustomRegistration;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FacultyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('faculty')
            ->path('faculty')
            ->login()
            ->registration(CustomRegistration::class)
            ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->authGuard('faculties')
            ->discoverResources(in: app_path('Filament/Faculty/Resources'), for: 'App\\Filament\\Faculty\\Resources')
            ->discoverPages(in: app_path('Filament/Faculty/Pages'), for: 'App\\Filament\\Faculty\\Pages')
            ->discoverWidgets(in: app_path('Filament/Faculty/Widgets'), for: 'App\\Filament\\Faculty\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->colors([
                'primary' => [
                    50 => "#803333",
                    100 => "#803333",
                    200 => "#803333",
                    300 => "#803333",
                    400 => "#803333",
                    500 => "#803333",
                    600 => "#803333",
                    700 => "#803333",
                    800 => "#803333",
                    900 => "#803333",
                    950 => "#803333",
                ],
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Documents'),
                NavigationGroup::make()
                    ->label('System')
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
