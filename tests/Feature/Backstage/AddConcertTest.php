<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function guests_cannot_view_the_concert_form()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
    }

    /**
     *  @test
     */
    public function promoters_can_view_the_add_concert_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /**
     *  @test
     */
    public function adding_a_valid_concert()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with the whatever',
            'additional_information' => 'You must be 21+ to attend',
            'date' => '2023-11-10',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St',
            'city' => 'Faketown',
            'state' => 'CA',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75'
        ]);

        tap(Concert::first(), function ($concert) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect("/concerts/{$concert->id}");

            $this->assertEquals('No Warning', $concert->title);
            $this->assertEquals('with the whatever', $concert->subtitle);
            $this->assertEquals('You must be 21+ to attend', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2023-11-10 8:00pm'), $concert->date);
            $this->assertEquals('The Mosh Pit', $concert->venue);
            $this->assertEquals('123 Fake St', $concert->venue_address);
            $this->assertEquals('Faketown', $concert->city);
            $this->assertEquals('CA', $concert->state);
            $this->assertEquals('12345', $concert->zip);
            $this->assertEquals(3250, $concert->ticket_price);
            $this->assertEquals(75, $concert->ticketsRemaining());
        });
    }

    /**
     *  @test
     */
    public function guests_cannot_add_a_concert()
    {
        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with the whatever',
            'additional_information' => 'You must be 21+ to attend',
            'date' => '2023-11-10',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St',
            'city' => 'Faketown',
            'state' => 'CA',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }
}