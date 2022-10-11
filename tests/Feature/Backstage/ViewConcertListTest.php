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

use function PHPUnit\Framework\assertEquals;

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

        // We can zip the two collections together which results in a 2D array with each internal
        // element composed of the two elements at the same positions in the corresponding collections.
        // We then check each pair to see if they are equal. $this refers to the collection.
        Collection::macro('assertEquals', function($items) {
            Assert::assertEquals(count($this), count($items));
            $this->zip($items)->each(function($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
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
        
        $unpublishedConcertA = FactoryHelpers::createUnpublished(['user_id' => $user->id]);
        $unpublishedConcertB = FactoryHelpers::createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = FactoryHelpers::createUnpublished(['user_id' => $user->id]);

        $response = $this->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publishedConcerts')->assertEquals([
            $publishedConcertA,
            $publishedConcertC
        ]);

        $response->data('unpublishedConcerts')->assertEquals([
            $unpublishedConcertA,
            $unpublishedConcertC
        ]);
    }
}