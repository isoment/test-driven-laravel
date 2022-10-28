<?php

namespace Tests\Unit\Listeners;

use App\Events\ConcertAdded;
use App\Jobs\ProcessPosterImage;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SchedulePosterImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function it_queues_a_job_to_process_a_poster_image_if_a_poster_image_is_present()
    {
        $this->withoutExceptionHandling();

        Queue::fake();

        $concert = FactoryHelpers::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png'
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function($job) use($concert) {
            return $job->concert->is($concert);
        });
    }

    /**
     *  @test
     */
    public function a_job_is_not_queued_if_a_poster_is_not_present()
    {
        $this->withoutExceptionHandling();

        Queue::fake();

        $concert = FactoryHelpers::createUnpublished([
            'poster_image_path' => NULL
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class, function($job) use($concert) {
            return $job->concert->is($concert);
        });
    }
}
