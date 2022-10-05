<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function promoters_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(200);
        $concertFromResponse = $response->original->getData()['concert'];
        $this->assertTrue($concertFromResponse->is($concert));
    }

    /**
     *  @test
     */
    public function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->published()->create([
            'user_id' => $user->id
        ]);

        $this->assertTrue($concert->isPublished());

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");
        $response->assertStatus(403);
    }

    /**
     *  @test
     */
    public function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");
        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function promoters_see_a_404_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get("/backstage/concerts/9999/edit");

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_any_concert()
    {
        $user = User::factory()->create();

        $concert = Concert::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     *  @test
     */
    public function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get("/backstage/concerts/9999/edit");
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     *  @test
     */
    public function promoters_can_edit_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2021-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->patch("/backstage/concerts/{$concert->id}", [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2022-02-02',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
            'ticket_quantity' => 10,
        ]);

        $response->assertRedirect("/backstage/concerts");

        tap($concert->fresh(), function($concert) {
            $this->assertEquals('New title', $concert->title);
            $this->assertEquals('New subtitle', $concert->subtitle);
            $this->assertEquals('New additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2022-02-02 8:00pm'), $concert->date);
            $this->assertEquals('New venue', $concert->venue);
            $this->assertEquals('New address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals(7250, $concert->ticket_price);
        });
    }

    /**
     *  @test
     */
    public function promoters_cannot_edit_other_unpublished_concerts()
    {
        // $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $otherUser->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2021-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->patch("/backstage/concerts/{$concert->id}", [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2022-02-02',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
            'ticket_quantity' => 10,
        ]);

        $response->assertStatus(404);

        tap($concert->fresh(), function($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2021-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }
}