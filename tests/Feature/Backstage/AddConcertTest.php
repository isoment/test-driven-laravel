<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Testing\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    private function validParam(array $overrides = []) : array
    {
        return array_merge(
            [            
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
            ],
            $overrides
        );
    }

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

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertStatus(302);
            $response->assertRedirect("/backstage/concerts");

            $this->assertTrue($concert->user->is($user));

            $this->assertFalse($concert->isPublished());

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
            $this->assertEquals(75, $concert->ticket_quantity);
            $this->assertEquals(0, $concert->ticketsRemaining());
        });
    }

    /**
     *  @test
     */
    public function guests_cannot_add_a_concert()
    {
        $response = $this->post('/backstage/concerts', $this->validParam());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function title_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['title' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('title');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function subtitle_is_optional()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => '',
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

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertStatus(302);
            $response->assertRedirect("/backstage/concerts");

            $this->assertTrue($concert->user->is($user));

            $this->assertNull($concert->subtitle);
        });
    }

    /**
     *  @test
     */
    public function additional_information_is_optional()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'subtitle',
            'additional_information' => '',
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

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertStatus(302);
            $response->assertRedirect("/backstage/concerts");

            $this->assertTrue($concert->user->is($user));

            $this->assertNull($concert->additional_information);
        });
    }

    /**
     *  @test
     */
    public function date_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['date' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function date_is_must_be_valid_date()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['date' => 'fake-date']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function time_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
        ->post('/backstage/concerts', $this->validParam(['time' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('time');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function time_must_be_a_valid_time()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['time' => 'fake-time']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('time');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function venue_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['venue' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('venue');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function venue_address_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['venue_address' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('venue_address');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function city_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['city' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('city');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function state_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['state' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('state');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function zip_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['zip' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('zip');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function ticket_price_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_price' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function ticket_price_must_be_numeric()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_price' => 'string']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function ticket_price_must_be_at_least_five()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_price' => '4.87']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function ticket_quantity_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_quantity' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function ticket_quantity_must_be_numeric()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_quantity' => 'string']));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function concert_must_have_at_least_one_ticket()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->fromUrl('/backstage/concerts/new')
            ->post('/backstage/concerts', $this->validParam(['ticket_quantity' => 0]));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(0, Concert::count());
    }

    /**
     *  @test
     */
    public function a_poster_image_is_uploaded_if_included()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = File::image('concert-poster.png');

        $response = $this->post('/backstage/concerts', $this->validParam([
            'poster_image' => $file,
        ]));

        $this->assertNotNull(Concert::first()->poster_image_path);

        Storage::disk('public')->assertExists(Concert::first()->poster_image_path);
        
        $this->assertFileEquals(
            $file->getPathname(),
            Storage::disk('public')->path(Concert::first()->poster_image_path)
        );
    }
}