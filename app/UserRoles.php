<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 31.10.2017
 * Time: 21:37
 */

namespace App;

/**
 * Trait UserRoles
 * @package App
 */
trait UserRoles
{
    /**
     * @param  string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        $roles = $this->roles->map(function ($role) {
            return $role->key;
        })->toArray();

        // Allow super admin do anything
        if (in_array(Role::SUPER_ADMIN, $roles)) return true;

        return in_array($role, $roles);
    }
}