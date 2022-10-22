<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendAttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use App\Models\AttendeeMessage;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function it_sends_the_message_to_all_concert_attendees()
    {
        // $this->withoutExceptionHandling();

        Mail::fake();

        $concert = FactoryHelpers::createPublished();

        $otherConcert = FactoryHelpers::createPublished();

        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My subject',
            'message' => 'My message',
        ]);

        $orderA = FactoryHelpers::createOrderForConcert($concert, ['email' => 'alex@example.com']);
        $orderB = FactoryHelpers::createOrderForConcert($concert, ['email' => 'sam@example.com']);
        $orderC = FactoryHelpers::createOrderForConcert($concert, ['email' => 'taylor@example.com']);
        $otherOrder = FactoryHelpers::createOrderForConcert($otherConcert, ['email' => 'jane@example.com']);

        SendAttendeeMessage::dispatch($message);

        // We want to assert that emails have been sent to the users in the orders above.
        Mail::assertSent(AttendeeMessageEmail::class, function($mail) use($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertSent(AttendeeMessageEmail::class, function($mail) use($message) {
            return $mail->hasTo('sam@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertSent(AttendeeMessageEmail::class, function($mail) use($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertNotSent(AttendeeMessageEmail::class, function($mail) {
            return $mail->hasTo('jane@example.com');
        });
    }
}