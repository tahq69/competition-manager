<?php

namespace Tests;

use App\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $roles;

    protected function createRoles()
    {
        foreach (Role::ALL_ROLES as $role) {
            Role::create(['key' => $role]);
        }
    }

    protected function syncRole(\App\User $user, string $role)
    {
        $user->roles()->sync([$this->findRoleId($role)]);
    }

    private function findRoleId($role_key)
    {
        if (!$this->roles) {
            $role_table = app(Role::class)->getTable();
            $this->roles = \DB::table($role_table)->get();
        }
        $result = 0;
        $this->roles->filter(function ($role) use ($role_key) {
            return $role->key == $role_key;
        })->map(function ($role) use (&$result) {
            $result = $role->id;
            return $role->id;
        });

        return $result;
    }
}
