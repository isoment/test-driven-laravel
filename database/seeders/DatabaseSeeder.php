<?php

namespace Database\Seeders;

use App\Models\Concert;
use App\Models\Ticket;
use App\Models\User;
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
        $user = User::factory()->create([
            'email' => 'test@test.com'
        ]);

        $concert = Concert::factory()->published()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 10
        ]);
    }
}
