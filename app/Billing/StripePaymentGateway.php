<?php

declare(strict_types=1);

namespace App\Billing;

use Illuminate\Support\Collection;

class StripePaymentGateway implements PaymentGateway
{
    private string $apiKey;
    private \Stripe\StripeClient $stripe;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->stripe = new \Stripe\StripeClient();
    }

    /**
     *  @param int $charge this amount to the customer
     *  @param string $token representing the customers payment method
     *  @throws \Stripe\Exception\InvalidRequestException
     *  @return void
     */
    public function charge(int $amount, string $token): void
    {
        try {
            $this->stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token
            ], ['api_key' => $this->apiKey]);
        } catch(\Stripe\Exception\InvalidRequestException $e) {
            throw new PaymentFailedException;
        }
    }

    /**
     *  We need to get a valid stripe payment token in order to make charges
     *  @return string
     */
    public function getValidTestToken() : string
    {
        return $this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => $this->apiKey])->id;
    }

    /**
     *  We are passing in a callback where we are performing some charges. We only want
     *  to return the charges performed in the callback. We do this by getting the latest
     *  charge, executing the callback and then returning a collection of charges that were
     *  performed in the callback
     *  @param callable $callback
     *  @return Illuminate\Support\Collection
     */
    public function newChargesDuring(callable $callback) : Collection
    {
        $latestCharge = $this->lastCharge();

        // The callback will try to charge the payment gateway.
        $callback();

        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    /**
     *  We want to get the last charge in order to determine our starting point when asserting
     *  against the new charge.
     */
    private function lastCharge()
    {
        return $this->stripe->charges->all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )['data'][0];
    }

    /**
     *  Get all the new charges after the parameter charge
     */
    private function newChargesSince($charge = null)
    {
        $newCharges = $this->stripe->charges->all(
            [
                'ending_before' => $charge ? $charge->id : null,
            ],
            ['api_key' => $this->apiKey]
        )['data'];

        return collect($newCharges);
    }
}