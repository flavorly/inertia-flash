<?php

namespace Flavorly\InertiaFlash;

use Flavorly\InertiaFlash\Inertia\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\ResponseFactory as InertiaResponseFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InertiaFlashServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('inertia-flash')
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        // Booting the package
    }

    public function registeringPackage()
    {
        // Register Singleton
        $this->app->singleton(InertiaFlash::class, fn ($app) => new InertiaFlash());

        // Extend the Inertia Default Response Factory
        $this->app->extend(InertiaResponseFactory::class, fn() => new ResponseFactory());

        // Append the Macro to forget specific Inertia Shared Keys
        Inertia::macro('forget', fn($keys) => Arr::forget($this->sharedProps,$keys));

        // Tweak the RedirectResponse to add the Inertia Flash
        RedirectResponse::macro('inertia', function($key, $value, bool $append = false): RedirectResponse {
            $key = is_array($key) ? $key : [$key => $value];
            foreach ($key as $k => $v) {
                inertia_flash()->share($k,$v,$append);
            }
            /** @var RedirectResponse $this */
            return $this;
        });
    }
}
