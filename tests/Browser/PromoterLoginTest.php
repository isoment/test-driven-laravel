<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PromoterLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function logging_in_successfully()
    {
        User::factory()->create([
            'email' => 'jane@test.com',
            'password' => bcrypt('password')
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'jane@test.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->assertPathIs('/backstage/concerts');

            // We want to clear the cookies after login so we don't get errors
            // for the next test.
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     *  @test
     */
    public function logging_in_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'jane@test.com',
            'password' => bcrypt('password')
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'jane@test.com')
                    ->type('password', 'wrong-pass')
                    ->press('Log in')
                    ->assertPathIs('/login')
                    ->assertSee('credentials do not match');
        });
    }
}
