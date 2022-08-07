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

    /**
     *  We need to get a valid stripe payment token in order to make charges charge
     *  @param \Stripe\StripeClient $stripe
     *  @return string
     */
    public function getValidTestToken() : string
    {
        $stripe = new \Stripe\StripeClient();

        return $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => $this->apiKey])->id;
    }
}