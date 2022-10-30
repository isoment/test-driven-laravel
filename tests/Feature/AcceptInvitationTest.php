<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function viewing_an_unused_invitation()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => NULL,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(200);
        $response->assertViewIs('invitations.show');
        $this->assertTrue($response->data('invitation')->is($invitation));
    }

    /**
     *  @test
     */
    public function viewing_a_used_invitation()
    {
        Invitation::factory()->create([
            'user_id' => User::factory()->create()->id,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function viewing_an_invitation_that_does_not_exist()
    {
        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function registering_with_a_valid_invitation_code()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => NULL,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $response->assertRedirect('/backstage/concerts');

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
    }

    /**
     *  @test
     */
    public function registering_with_a_used_invitation_code()
    {
        Invitation::factory()->create([
            'user_id' => User::factory()->create()->id,
            'code' => 'TESTCODE1234',
        ]);

        $this->assertEquals(1, User::count());

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $response->assertStatus(404);
        $this->assertEquals(1, User::count());
    }

    /**
     *  @test
     */
    public function registering_with_an_invitation_code_that_does_not_exist()
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'FAKECODE1234'
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, User::count());
    }

    /**
     *  @test
     */
    public function the_email_is_required()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => NULL,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => NULL,
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /**
     *  @test
     */
    public function the_email_is_must_have_correct_format()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => NULL,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => 'random string',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /**
     *  @test
     */
    public function the_email_must_be_unique()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);

        $this->assertEquals(1, User::count());

        $invitation = Invitation::factory()->create([
            'user_id' => NULL,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::count());
    }
}
