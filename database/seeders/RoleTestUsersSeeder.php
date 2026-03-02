<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testUsers = [
            [
                'email' => 'admin@example.com',
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Admin',
                'address' => 'Main Center',
                'contact_no' => '09000000001',
                'gender' => Gender::Male->value,
                'role' => UserRole::Admin->value,
                'status' => UserStatus::Active->value,
                'parent_name' => null,
            ],
            [
                'email' => 'therapist@example.com',
                'first_name' => 'Thera',
                'middle_name' => null,
                'last_name' => 'Pist',
                'address' => 'Main Center',
                'contact_no' => '09000000002',
                'gender' => Gender::Female->value,
                'role' => UserRole::Therapist->value,
                'status' => UserStatus::Active->value,
                'parent_name' => null,
            ],
            [
                'email' => 'assistant@example.com',
                'first_name' => 'Assis',
                'middle_name' => null,
                'last_name' => 'Tant',
                'address' => 'Main Center',
                'contact_no' => '09000000003',
                'gender' => Gender::Male->value,
                'role' => UserRole::Assistant->value,
                'status' => UserStatus::Active->value,
                'parent_name' => null,
            ],
            [
                'email' => 'frontdesk@example.com',
                'first_name' => 'Front',
                'middle_name' => null,
                'last_name' => 'Desk',
                'address' => 'Main Center',
                'contact_no' => '09000000004',
                'gender' => Gender::Female->value,
                'role' => UserRole::FrontDesk->value,
                'status' => UserStatus::Active->value,
                'parent_name' => null,
            ],
            [
                'email' => 'client@example.com',
                'first_name' => 'Sample',
                'middle_name' => null,
                'last_name' => 'Client',
                'address' => 'Main Center',
                'contact_no' => '09000000005',
                'gender' => Gender::Male->value,
                'role' => UserRole::Client->value,
                'status' => UserStatus::Active->value,
                'parent_name' => 'Client Parent',
            ],
        ];

        foreach ($testUsers as $testUser) {
            User::query()->updateOrCreate(
                ['email' => $testUser['email']],
                array_merge($testUser, [
                    'password' => 'password',
                    'must_change_password' => true,
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
