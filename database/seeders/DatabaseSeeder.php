<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\Trash;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin Hebat',
            'password' => Hash::make('secret321'),
        ]);

        // Create some random users
        $users = User::factory(5)->create();

        // Add admin to users collection
        $allUsers = collect([$admin])->merge($users);

        // Create trashes
        Trash::factory(20)->create();

        // Create reports for users
        foreach ($allUsers as $user) {
            Report::factory(rand(1, 5))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
