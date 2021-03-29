<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run(): void
    {
        User::whereEmail('admin@xcrawler.local')->forceDelete();

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@xcrawler.local',
            'password' => Hash::make('admin'),
        ]);

        $admin->attachRole('admin');

        User::whereEmail('user@xcrawler.local')->forceDelete();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@xcrawler.local',
            'password' => Hash::make('user'),
        ]);

        $user->attachRole('user');
    }
}
