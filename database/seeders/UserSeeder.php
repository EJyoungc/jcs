<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // System Administrator
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
            'role' => 'system_administrator',
            'status' => 'active',
        ]);

        // Chairperson
        User::create([
            'name' => 'Chairperson User',
            'email' => 'chairperson@gmail.com',
            'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
            'role' => 'chairperson',
            'status' => 'active',
        ]);

        // JTC Members
        foreach (range(1, 3) as $index) {
            User::create([
                'name' => "JTC Member {$index}",
                'email' => "jtc{$index}@gmail.com",
                'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
                'role' => 'jtc_member',
                'status' => 'active',
            ]);
        }

        // Training Officers
        foreach (range(1, 2) as $index) {
            User::create([
                'name' => "Training Officer {$index}",
                'email' => "officer{$index}@gmail.com",
                'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
                'role' => 'training_officer',
                'status' => 'active',
            ]);
        }

        // District Court Clerks
        foreach (range(1, 2) as $index) {
            User::create([
                'name' => "Court Clerk {$index}",
                'email' => "clerk{$index}@gmail.com",
                'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
                'role' => 'district_court_clerk',
                'status' => 'active',
            ]);
        }

        // Candidates
        foreach (range(1, 5) as $index) {
            User::create([
                'name' => "Candidate {$index}",
                'email' => "c{$index}@gmail.com",
                'password' => Hash::make('root'), // Consider using a more secure default password or environment variable
                'role' => 'candidate',
                'status' => 'active',
            ]);
        }
    }
}
