<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Wyco', 'email' => 'wyco@example.com', 'password' => Hash::make('password')],
            ['name' => 'Joe', 'email' => 'joe@example.com', 'password' => Hash::make('password')],
            ['name' => 'Anakin', 'email' => 'anakin@example.com', 'password' => Hash::make('password')],
            ['name' => 'Saw', 'email' => 'saw@example.com', 'password' => Hash::make('password')],
            ['name' => 'Shiro', 'email' => 'shiro@example.com', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}