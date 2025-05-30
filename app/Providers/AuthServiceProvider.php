<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        foreach(config('abilities') as $code => $lable ){
            Gate::define($code, function($user) use ($code){
                return $user->hasAbility($code); // concerns
            });
        }



    }
}
