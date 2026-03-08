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
            ['name' => 'Wyco', 'email' => 'wyco@example.com', 'password' => Hash::make('password'), 'phone_num' => '555-0101', 'address' => '123 Main St'],
            ['name' => 'Joe', 'email' => 'joe@example.com', 'password' => Hash::make('password'), 'phone_num' => '555-0102', 'address' => '456 Oak Ave'],
            ['name' => 'Anakin', 'email' => 'anakin@example.com', 'password' => Hash::make('password'), 'phone_num' => '555-0103', 'address' => '789 Elm Blvd'],
            ['name' => 'Saw', 'email' => 'saw@example.com', 'password' => Hash::make('password'), 'phone_num' => '555-0104', 'address' => '321 Pine Rd'],
            ['name' => 'Shiro', 'email' => 'shiro@example.com', 'password' => Hash::make('password'), 'phone_num' => '555-0105', 'address' => '654 Cedar Ln'],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}