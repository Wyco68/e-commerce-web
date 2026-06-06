<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedDemoCustomer();
    }

    private function seedAdmin(): void
    {
        $password = config('app.demo_admin_password') ?? Str::password(16);

        $admin = User::firstOrCreate(
            ['email' => 'admin@carpart.test'],
            [
                'name' => 'Admin User',
                'password' => $password,
                'phone_num' => '+10000000001',
                'address' => 'Admin Office',
            ]
        );

        $admin->forceFill([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ])->save();
    }

    private function seedDemoCustomer(): void
    {
        $password = config('app.demo_user_password')
            ?? config('app.demo_admin_password')
            ?? Str::password(16);

        $user = User::firstOrCreate(
            ['email' => 'user@carpart.test'],
            [
                'name' => 'Test Customer',
                'password' => $password,
                'phone_num' => '+1234567890',
                'address' => '123 Main Street, Test City',
            ]
        );

        $user->forceFill([
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
    }
}
