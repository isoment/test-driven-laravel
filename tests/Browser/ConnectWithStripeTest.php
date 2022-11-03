<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ConnectWithStripeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     *  @test
     */
    public function connecting_a_stripe_account_successfully()
    {
        $user = User::factory()->create([
            'stripe_account_id' => NULL,
            'stripe_access_token' => NULL,
        ]);

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                    ->visit('/backstage/stripe-connect/authorize')
                    ->assertUrlIs('https://connect.stripe.com/oauth/v2/authorize')
                    ->assertQueryStringHas('response_type', 'code')
                    ->assertQueryStringHas('scope', 'read_write')
                    ->assertQueryStringHas('client_id', config('services.stripe.client_id'));
        });
    }
}
