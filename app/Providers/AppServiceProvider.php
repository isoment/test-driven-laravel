<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // We tell laravel which payment gateway to use and how to construct it.
        $this->app->bind(StripePaymentGateway::class, function() {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        // Anytime something asks for the PaymentGateway interface provide the StripePaymentGateway
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
