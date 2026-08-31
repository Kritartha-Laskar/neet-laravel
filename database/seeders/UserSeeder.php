<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['gmail' => 'admin@gmail.com'],
            [
                'name'      => 'Admin User',
                'user_name' => 'admin',
                'password'  => bcrypt('12345678'),
                'user_type' => 'admin',
                'role'      => 7,
                'status'    => 'active',
                'phone_no'  => '9876543210',
            ]
        );

        $this->command->info('✅ Admin User created successfully (admin@gmail.com / 12345678)');
    }
}
