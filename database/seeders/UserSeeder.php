<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /** Matches README demo credentials. */
    public const DEMO_ADMIN_EMAIL = 'admin@carpart.test';

    public const DEMO_USER_EMAIL = 'user@carpart.test';

    public const DEMO_PASSWORD = 'password';

    public function run(): void
    {
        $this->seedAdmin();
        $this->seedDemoCustomer();
    }

    private function seedAdmin(): void
    {
        $data = [
            'name' => 'Admin User',
            'password' => $this->passwordForDemoAccounts(),
            'phone_num' => '+10000000001',
            'address' => 'Admin Office',
        ];

        $admin = $this->usesDemoReset()
            ? User::updateOrCreate(['email' => self::DEMO_ADMIN_EMAIL], $data)
            : User::firstOrCreate(['email' => self::DEMO_ADMIN_EMAIL], $data);

        $admin->forceFill([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ])->save();
    }

    private function seedDemoCustomer(): void
    {
        $data = [
            'name' => 'Test Customer',
            'password' => $this->passwordForDemoAccounts(),
            'phone_num' => '+1234567890',
            'address' => '123 Main Street, Test City',
        ];

        $user = $this->usesDemoReset()
            ? User::updateOrCreate(['email' => self::DEMO_USER_EMAIL], $data)
            : User::firstOrCreate(['email' => self::DEMO_USER_EMAIL], $data);

        $user->forceFill([
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
    }

    private function usesDemoReset(): bool
    {
        return (bool) config('app.demo_mode');
    }

    private function passwordForDemoAccounts(): string
    {
        if ($this->usesDemoReset()) {
            return self::DEMO_PASSWORD;
        }

        return config('app.demo_admin_password') ?? self::DEMO_PASSWORD;
    }
}
