<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Http\Request;

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

    //  public function boot(UrlGenerator $url)
    // {
    //     if (env('APP_ENV') !== 'local') {
    //         $url->forceScheme('https');
    //     }
        
    //     // Modifiez cette partie
    //     if (env('APP_ENV') === 'local') {
    //         Request::setTrustedProxies(
    //             ['127.0.0.1', '::1'],
    //             Request::HEADER_X_FORWARDED_FOR | 
    //             Request::HEADER_X_FORWARDED_HOST | 
    //             Request::HEADER_X_FORWARDED_PORT | 
    //             Request::HEADER_X_FORWARDED_PROTO
    //         );
    //     }
    // }
    public function boot(UrlGenerator $url)
{
    // Request::setTrustedProxies(
    //     ['127.0.0.1', '::1', '192.168.1.0/24'],
    //     Request::HEADER_X_FORWARDED_FOR | 
    //     Request::HEADER_X_FORWARDED_HOST | 
    //     Request::HEADER_X_FORWARDED_PORT | 
    //     Request::HEADER_X_FORWARDED_PROTO
    // );
}
}
