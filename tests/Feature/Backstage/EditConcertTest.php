<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
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

        $concert = Concert::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");
        $response->assertStatus(200);
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /**
     *  @test
     */
    public function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->create([
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
        $response = $this->get("/backstage/concerts/{$concert->id}/edit");
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}