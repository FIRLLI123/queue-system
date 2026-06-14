<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OrderType;
use App\Models\QueuePosition;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Order Types
        $orderTypes = ['CRM', 'CMS', 'OTHER'];
        foreach ($orderTypes as $name) {
            OrderType::firstOrCreate(['name' => $name], ['status' => 'ACTIVE']);
        }

        // 2. Seed Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@ccqueue.com',
                'password' => 'password',
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
            ]
        );

        $ccUsersData = [
            [
                'name' => 'Customer Care A1',
                'username' => 'a1',
                'email' => 'a1@ccqueue.com',
                'password' => 'password',
                'role' => 'CC',
                'status' => 'ACTIVE',
                'queue_number' => 1,
            ],
            [
                'name' => 'Customer Care B1',
                'username' => 'b1',
                'email' => 'b1@ccqueue.com',
                'password' => 'password',
                'role' => 'CC',
                'status' => 'ACTIVE',
                'queue_number' => 2,
            ],
            [
                'name' => 'Customer Care C1',
                'username' => 'c1',
                'email' => 'c1@ccqueue.com',
                'password' => 'password',
                'role' => 'CC',
                'status' => 'ACTIVE',
                'queue_number' => 3,
            ]
        ];

        foreach ($ccUsersData as $data) {
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => $data['role'],
                    'status' => $data['status'],
                ]
            );

            // Seed active Queue Position if not already present
            QueuePosition::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'ACTIVE'],
                ['queue_number' => $data['queue_number']]
            );
        }
    }
}
