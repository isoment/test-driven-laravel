<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Database\Helpers\FactoryHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        // When used in a closure the $this keyword will refer to the class of the method that
        // accepts the closure as a param, in this case TestResponse.
        TestResponse::macro('data', function($key) {
            return $this->original->getData()[$key];
        });

        // Phpunit assertions are all static methods so we can call them directly from the class.
        // $this context refers to Collection.
        Collection::macro('assertContains', function($value) {
            Assert::assertTrue(
                $this->contains($value), 
                "Failed asserting that the collection contained the specified value"
            );
        });

        Collection::macro('assertNotContains', function($value) {
            Assert::assertFalse(
                $this->contains($value),
                "Failed asserting that the collection does not contain the specified value"
            );
        });
    }

    /**
     *  @test
     */
    public function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     *  @test
     */
    public function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $publishedConcertA = FactoryHelpers::createPublished(['user_id' => $user->id]);
        $publishedConcertB = FactoryHelpers::createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = FactoryHelpers::createPublished(['user_id' => $user->id]);
        
        $unpublishedConcertA = Concert::factory()->unpublished()->create(['user_id' => $user->id]);
        $unpublishedConcertB = Concert::factory()->unpublished()->create(['user_id' => $otherUser->id]);
        $unpublishedConcertC = Concert::factory()->unpublished()->create(['user_id' => $user->id]);

        $response = $this->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publishedConcerts')->assertContains($publishedConcertA);
        $response->data('publishedConcerts')->assertNotContains($publishedConcertB);
        $response->data('publishedConcerts')->assertContains($publishedConcertC);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertA);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertC);

        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertA);
        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertB);
        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertC);
        $response->data('unpublishedConcerts')->assertContains($unpublishedConcertA);
        $response->data('unpublishedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('unpublishedConcerts')->assertContains($unpublishedConcertC);
    }
}