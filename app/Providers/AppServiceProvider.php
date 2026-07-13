<?php

namespace App\Providers;

use App\Events\OrderValidatedByClient;
use App\Listeners\CreditAgentOnOrderValidation;
use App\Mail\BrevoApiTransport;
use App\Models\ExchangeRate;
use App\Models\Meal;
use App\Models\SubscriptionType;
use App\Observers\ExchangeRateObserver;
use App\Observers\MealObserver;
use App\Observers\SubscriptionTypeObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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
        Mail::extend('brevo', function (array $config) {
            return new BrevoApiTransport($config['key'] ?? env('BREVO_API_KEY'));
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(
            OrderValidatedByClient::class,
            CreditAgentOnOrderValidation::class,
        );

        // Register observers for automatic notifications
        Meal::observe(MealObserver::class);
        SubscriptionType::observe(SubscriptionTypeObserver::class);
        ExchangeRate::observe(ExchangeRateObserver::class);
    }
}
