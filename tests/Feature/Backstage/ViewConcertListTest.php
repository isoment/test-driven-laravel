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