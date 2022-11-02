<?php

namespace Tests\Unit\Mail;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvitationEmailTest extends TestCase
{
    /**
     *  @test
     */
    public function the_email_contains_a_link_to_accept_the_invitation()
    {
        $invitation = Invitation::factory()->make([
            'email' => 'john@example.com',
            'code' => 'TESTCODE1234',
        ]);

        $email = new InvitationEmail($invitation);
        $linkURL = url('/invitations/TESTCODE1234');

        $this->assertStringContainsString($linkURL, $email->render());
    }

    /**
     *  @test
     */
    public function email_has_the_correct_subject()
    {
        $invitation = Invitation::factory()->make();

        $email = new InvitationEmail($invitation);

        $this->assertStringContainsString('You are invited to sell tickets', $email->build()->subject);
    }
}
