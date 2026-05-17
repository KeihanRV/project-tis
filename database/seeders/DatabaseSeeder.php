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
        $admin = User::create([
            'name' => 'Admin Hebat',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret321'),
            'role' => 'admin',
        ]);

        // Create 5 regular users with 'user' role
        $users = User::factory(5)->create(['role' => 'user']);

        // Combine admin and regular users
        $allUsers = collect([$admin])->merge($users);

        // Create trashes
        Trash::factory(20)->create();

        // Create reports for users with trash relationships
        foreach ($allUsers as $user) {
            Report::factory(rand(1, 5))->withTrash()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
