<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\HashidsTicketCodeGenerator;
use App\InvitationCodeGenerator;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\TicketCodeGenerator;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        // We tell laravel which payment gateway to use and how to construct it.
        $this->app->bind(StripePaymentGateway::class, function() {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        // Let's tell laravel how we will build the ticket code generator
        $this->app->bind(HashidsTicketCodeGenerator::class, function() {
            return new HashidsTicketCodeGenerator(config('app.ticket_code_salt'));
        });

        // Anytime something asks for the PaymentGateway interface provide the StripePaymentGateway
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        // Anytime OrderConfirmationNumberGenerator is requested provide RandomOrderConfirmationNumberGenerator
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        // Since RandomOrderConfirmationNumberGenerator also implements the InvitationCodeGenerator interface
        // we can bind InvitationCodeGenerator.
        $this->app->bind(InvitationCodeGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        // Anytime TicketCodeGenerator is requested provide HashidsTicketCodeGenerator
        $this->app->bind(TicketCodeGenerator::class, HashidsTicketCodeGenerator::class);
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
