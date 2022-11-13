<?php

declare(strict_types=1);

namespace App\Billing;

use Illuminate\Support\Collection;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    private string $apiKey;
    private \Stripe\StripeClient $stripe;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->stripe = new \Stripe\StripeClient();
    }

    /**
     *  We create a charge for the customers ticket purchase. 90% of the charge goes to
     *  The promoter and we keep the remainder as a fee.
     *  @param int $charge this amount to the customer
     *  @param string $token representing the customers payment method
     *  @param string $destinationAccountId the stripe account to deposit the charge into
     *  @throws \Stripe\Exception\InvalidRequestException
     *  @return App\Billing\Charge
     */
    public function charge(int $amount, string $token, string $destinationAccountId) : Charge
    {
        try {
            $stripeCharge = $this->stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
                'destination' => [
                    'account' => $destinationAccountId,
                    'amount' => $amount * 0.9
                ]
            ], ['api_key' => $this->apiKey]);

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['payment_method_details']['card']['last4'],
                'destination' => $destinationAccountId
            ]);
        } catch(\Stripe\Exception\InvalidRequestException $e) {
            throw new PaymentFailedException;
        }
    }

    /**
     *  We need to get a valid stripe payment token in order to make charges
     *  @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER) : string
    {
        return $this->stripe->tokens->create([
            'card' => [
                'number' => $cardNumber,
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

        return $this->newChargesSince($latestCharge)->map(function($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['payment_method_details']['card']['last4'],
            ]);
        });
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