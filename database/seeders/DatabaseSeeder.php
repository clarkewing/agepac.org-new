<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Appleseed',
            'email' => 'admin@clarkewing.dev',
        ]);

        User::factory()->create([
            'first_name' => 'Robin',
            'last_name' => 'Banks',
            'email' => 'user@clarkewing.dev',
        ]);
    }
}
