<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Stripe\Account;
use Tests\DuskTestCase;

class ConnectWithStripeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     *  @test
     *  This test does not work. Fails when clicking the Skip this form button on stripe.
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
                    ->assertQueryStringHas('client_id', config('services.stripe.client_id'))
                    ->waitForReload(function(Browser $browser) {
                        $browser->press("Skip this form");
                    });

            tap($user->fresh(), function($user) {
                $this->assertNotNull($user->stripe_account_id);
                $this->assertNotNull($user->stripe_access_token);

                // We want to get the stripe account id to compare it to what we store on the
                // users table. We can get it by passing the stripe access token.
                $connectedAccount = Account::retrieve(null, [
                    'api_key' => $user->stripe_access_token
                ]);

                $this->assertEquals($connectedAccount->id, $user->stripe_account_id);
            });
        });
    }
}
