<?php

namespace App\Providers;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use DateTime; // Import DateTime

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

        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('jcs/livewire/update', $handle);
        });
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('jcs/livewire/livewire.js', $handle);
        });

        
        //
    }
}
