<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PromoterLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function logging_in_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertAuthenticated();
        $this->assertTrue(Auth::user()->is($user));
    }

    /**
     *  @test
     */
    public function logging_in_with_invalid_credentials_fails()
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('password')
        ]);

        $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'incorrect-password'
        ]);

        $this->assertGuest();
    }

    /**
     *  @test
     */
    public function logging_in_with_an_account_that_does_not_exist_fails()
    {
        $this->post('/login', [
            'email' => 'fakeuser@example.com',
            'password' => 'password'
        ]);

        $this->assertGuest();
    }
}