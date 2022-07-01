<?php

namespace Tests\Feature;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function a_user_can_view_a_published_concert_listing()
    {
        $concert = Concert::factory()->published()->create([
            'title' => 'The Red Chord',
            'subtitle' => 'With Animosity and Lethargy',
            'date' => Carbon::parse('December 13th, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Ln',
            'city' => 'Laraville',
            'state' => 'NY',
            'zip' => '01111',
            'additional_information' => 'Lorem Ipson whatever lkfdsof',
        ]);

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(200)
            ->assertSee('The Red Chord')
            ->assertSee('With Animosity and Lethargy')
            ->assertSee('December 13, 2016')
            ->assertSee('8:00pm')
            ->assertSee('32.50')
            ->assertSee('The Mosh Pit')
            ->assertSee('123 Example Ln')
            ->assertSee('Laraville, NY 01111')
            ->assertSee('Lorem Ipson whatever lkfdsof');
    }

    /**
     *  @test
     */
    public function user_cannot_view_unpublished_concert_listings()
    {
        $concert = Concert::factory()->unpublished()->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);
    }
}
