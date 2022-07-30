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

    /**
     *  @param int $charge this amount to the customer
     *  @param string $token representing the customers payment method
     *  @throws \Stripe\Exception\InvalidRequestException
     *  @return void
     */
    public function charge(int $amount, string $token): void
    {
        $stripe = new \Stripe\StripeClient();

        try {
            $stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token
            ], ['api_key' => $this->apiKey]);
        } catch(\Stripe\Exception\InvalidRequestException $e) {
            throw new PaymentFailedException;
        }
    }
}