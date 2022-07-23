<?php

namespace Database\Factories;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'concert_id' => function() {
                return Concert::factory()->create()->id;
            }
        ];
    }

    /**
     *  Set the ticket to reserved.
     *  @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function reserved() : Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'reserved_at' => Carbon::now(),
            ];
        });
    }
}
