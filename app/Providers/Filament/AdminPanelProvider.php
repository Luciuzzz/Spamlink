<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureWizardCompleted;
use Andreia\FilamentNordTheme\FilamentNordThemePlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            // Branding
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandName('SpamLink')
            ->plugin(FilamentNordThemePlugin::make())
            ->viteTheme('resources/css/filament/admin-theme.css')
            ->colors([
                'primary' => Color::Yellow,
            ])
            ->maxContentWidth(Width::Full)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => <<<'HTML'
                <style>
                    .fi-main {
                        max-width: none !important;
                        width: 100%;
                    }
                    .fi-page,
                    .fi-page-content,
                    .fi-sc,
                    .fi-sc-component,
                    .fi-section,
                    .fi-section-content {
                        width: 100%;
                        max-width: none;
                    }

                    /* Keep the app's dark palette while using Nord theme structure. */
                    .dark .fi-layout,
                    .dark .fi-body,
                    .dark .fi-topbar nav,
                    .dark .fi-sidebar-header {
                        background-color: rgb(17 24 39) !important;
                    }

                    .dark .fi-section,
                    .dark .fi-wi-widget,
                    .dark .fi-ta-ctn,
                    .dark .fi-dropdown-panel,
                    .dark .fi-modal-window {
                        background-color: rgb(31 41 55) !important;
                        border-color: rgb(55 65 81) !important;
                    }

                </style>
            HTML)

            // Resources
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            // Pages
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            ->pages([
                Pages\Dashboard::class,
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            ->widgets([
                \App\Filament\Widgets\MyLandingButton::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])

            // Middleware HTTP del panel
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
                EnsureWizardCompleted::class,
            ])

            // Auth middleware
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
