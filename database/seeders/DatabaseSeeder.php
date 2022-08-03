<?php

namespace Database\Seeders;

use App\Models\Concert;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $concert = Concert::factory()->published()->create();

        Ticket::factory()->count(10)->create([
            'concert_id' => $concert->id
        ]);
    }
}
