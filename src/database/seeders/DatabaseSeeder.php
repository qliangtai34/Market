<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
{
    \App\Models\User::factory()->create([
        'name' => '出品者ユーザー',
        'email' => 'seller@example.com',
        'password' => bcrypt('password')
    ]);

    $this->call(ItemSeeder::class);
}

}
