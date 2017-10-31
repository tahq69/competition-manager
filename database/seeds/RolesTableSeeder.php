<?php

use App\Role;
use Illuminate\Database\Seeder;

/**
 * Class RolesTableSeeder
 */
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        foreach (Role::ALL_ROLES as $role) {
            Role::create(['key' => $role]);
        }
    }
}
