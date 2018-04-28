<?php namespace App;

use App\Contracts\UserRole;

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
        if (in_array(UserRole::SUPER_ADMIN, $roles)) return true;

        return in_array($role, $roles);
    }
}
