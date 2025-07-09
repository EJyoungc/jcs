<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //


        $roles = ['admin', 'procurement_officer', 'department_head', 'supplier', 'finance_officer'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }


    }
}
