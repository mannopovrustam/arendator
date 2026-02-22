<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
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
        $share = [
            'name' => 'name_'.session()->get('locale','ru'),
            'user' => Session::get('user'),
        ];
        View::share($share);
//        View::share('name', 'name_'.session()->get('locale','ru'));
        $this->bootEgovMusicSocialite();
    }

    private function bootEgovMusicSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('egov',function ($app) use ($socialite) {
            $config = $app['config']['services.egov'];
            return $socialite->buildProvider(EgovProvider::class, $config);
        });
    }

}
