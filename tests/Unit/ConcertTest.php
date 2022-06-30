<?php

namespace Tests\Unit;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function can_get_formatted_date()
    {
        $concert = Concert::factory()->create([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /**
     *  @test
     */
    public function can_get_formatted_start_time()
    {
        $concert = Concert::factory()->create([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /**
     *  @test
     */
    public function can_get_ticker_price_in_dollars()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }
}
