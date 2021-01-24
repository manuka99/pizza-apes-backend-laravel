<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            'name' => 'Admin',
        ]);
        Role::insert([
            'name' => 'Editor',
        ]);
        Role::insert([
            'name' => 'Author',
        ]);
    }
}
