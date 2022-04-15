<?php

namespace App\Providers;

use App\Repositories\Contracts\ICreditCardRepository;
use App\Repositories\CreditCardRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ICreditCardRepository::class, CreditCardRepository::class);
    }

    public function boot()
    {
    }
}
