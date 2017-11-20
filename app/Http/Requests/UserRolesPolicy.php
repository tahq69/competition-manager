<?php namespace App\Http\Requests;

use App\Role;
use App\User;

/**
 * Class UserRolesValidationRequest
 * @package App\Http\Requests
 */
class UserRolesPolicy
{
    /**
     * Get user roles key array.
     * @param User $user
     * @return array
     */
    public static function roles(User $user)
    {
        $roles = $user->roles->map(function ($role) {
            return $role->key;
        })->toArray();

        return $roles;
    }

    /**
     * @param array $roles
     * @param $role
     * @return bool
     */
    public static function hasRole($roles = [], $role)
    {
        // Allow super admin do anything
        if (in_array(Role::SUPER_ADMIN, $roles)) return true;

        if (in_array($role, $roles)) return true;

        return false;
    }

    /**
     * @param array $current
     * @param array $searchFor
     * @return bool
     */
    public static function hasAnyRole($current = [], $searchFor = [])
    {
        foreach ($searchFor as $role)
            if (UserRolesPolicy::hasRole($current, $role))
                return true;

        return false;
    }
}