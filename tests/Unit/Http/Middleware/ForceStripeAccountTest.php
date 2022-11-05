<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\ForceStripeAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ForceStripeAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function users_without_a_stripe_account_are_forced_to_connect_with_stripe()
    {
        // Create a user and act as that user
        $user = User::factory()->create([
            'stripe_account_id' => NULL
        ]);
        $this->actingAs($user);

        // Instantiate the middleware
        $middleware = new ForceStripeAccount;

        // All middleware has a handle() method that expects a request as the first argument ($request) and
        // a callback as the second argument ($next).
        $response = $middleware->handle(new Request(), function($request) {
            $this->fail('The next middleware was called when it should not have been');
        });

        // Assert that the response is a redirect response
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Assert that the redirect URL is the correct one
        $this->assertEquals(route('backstage.stripe-connect.connect'), $response->getTargetUrl());
    }
}
