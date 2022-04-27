<?php

namespace Igerslike\InertiaFlash;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
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

    public function registeringPackage()
    {
        // Register Singleton
        $this->app->singleton(InertiaFlash::class, fn ($app) => new InertiaFlash());

        // Append the Macro to forget specific Inertia Shared Keys
        Inertia::macro('forget', fn($keys) => Arr::forget($this->sharedProps,$keys));

        // Tweak the RedirectResponse to add the Inertia Flash
        RedirectResponse::macro('inertia',function($key,$value, bool $append = false){
            $key = is_array($key) ? $key : [$key => $value];
            foreach ($key as $k => $v) {
                app(InertiaFlash::class)->share($k,$v,$append);
            }
            return $this;
        });
    }
}
