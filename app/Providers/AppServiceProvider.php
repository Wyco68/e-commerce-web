<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            $appUrl = config('app.url');
            // Avoid forcing localhost as root on Render/production — breaks asset URLs in the browser
            if ($appUrl && ! str_contains($appUrl, 'localhost') && ! str_contains($appUrl, '127.0.0.1')) {
                URL::forceRootUrl($appUrl);
            }
        }

        if (config('app.force_insecure_cookies')) {
            config([
                'session.secure' => false,
                'session.same_site' => 'lax',
            ]);
        } elseif ($this->app->environment('production')) {
            config([
                'session.secure' => filter_var(env('SESSION_SECURE_COOKIE', true), FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        if (config('app.demo_mode')) {
            config([
                'session.lifetime' => config('app.demo_session_lifetime'),
            ]);
        }

        $this->ensurePublicStorageLink();
    }

    private function ensurePublicStorageLink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (file_exists($link) || ! is_dir($target)) {
            return;
        }

        try {
            symlink($target, $link);
        } catch (\Throwable) {
            // Fallback route in web.php serves /storage/* when symlink cannot be created.
        }
    }
}
