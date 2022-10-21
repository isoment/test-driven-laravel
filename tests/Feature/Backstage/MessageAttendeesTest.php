<?php

namespace Tests\Feature\Backstage;

use App\Jobs\SendAttendeeMessage;
use App\Models\AttendeeMessage;
use App\Models\User;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageAttendeesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function a_promoter_can_view_the_message_form_for_their_own_concert()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => $user->id
        ]);

        $response = $this->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertStatus(200);
        $response->assertViewIs('backstage.concert-messages.new');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /**
     *  @test
     */
    public function a_promoter_cannot_view_the_message_form_for_another_concert()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function a_guest_cannot_view_the_message_form_for_any_concert()
    {
        $concert = FactoryHelpers::createPublished();

        $response = $this->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertRedirect('/login');
    }

    /**
     *  @test
     */
    public function a_promoter_can_send_a_new_message()
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => $user->id
        ]);

        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();
        $this->assertEquals($concert->id, $message->concert_id);
        $this->assertEquals('My Subject', $message->subject);
        $this->assertEquals('My Message', $message->message);

        Queue::assertPushed(SendAttendeeMessage::class, function($job) use($message) {
            return $job->attendeeMessage->is($message);
        });
    }

    /**
     *  @test
     */
    public function a_promoter_cannot_send_a_new_message_for_other_concerts()
    {
        Queue::fake();

        $concert = FactoryHelpers::createPublished();

        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $response->assertRedirect("/login");
        $this->assertEquals(0, AttendeeMessage::count());

        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /**
     *  @test
     */
    public function subject_is_required()
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => $user->id
        ]);

        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => '',
            'message' => 'My Message',
        ]);

        $response->assertSessionHasErrors('subject');
        $this->assertEquals(0, AttendeeMessage::count());

        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /**
     *  @test
     */
    public function message_is_required()
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => $user->id
        ]);

        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => '',
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertEquals(0, AttendeeMessage::count());

        Queue::assertNotPushed(SendAttendeeMessage::class);
    }
}