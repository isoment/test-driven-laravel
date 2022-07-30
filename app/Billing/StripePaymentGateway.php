<?php

declare(strict_types=1);

namespace App\Billing;

class StripePaymentGateway implements PaymentGateway
{
    private string $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge(int $amount, string $token): void
    {
        $stripe = new \Stripe\StripeClient();

        $stripe->charges->create([
            'amount' => $amount,
            'currency' => 'usd',
            'source' => $token
        ], ['api_key' => $this->apiKey]);
    }
}