<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concert>
 */
class ConcertFactory extends Factory
{
    /**
     *  Define the model's default state.
     *
     *  @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => 'Example Band',
            'subtitle' => 'With the Fake Openers',
            'date' => Carbon::parse('+2 weeks'),
            'venue' => 'The Example Theatre',
            'venue_address' => '123 Example Ln',
            'city' => 'Fakeville',
            'state' => 'NY',
            'zip' => '90210',
            'additional_information' => 'Some sample additional information',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ];
    }

    /**
     *  Set the Concert factory state to published
     *  @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published() : Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => Carbon::parse('-1 week'),
            ];
        });
    }

    /**
     *  Set the Concert factory state to unpublished
     *  @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpublished() : Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => NULL,
            ];
        });
    }
}
