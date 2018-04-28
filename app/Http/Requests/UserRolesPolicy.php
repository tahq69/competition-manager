<?php namespace App\Http\Requests;

use App\Contracts\UserRole;
use App\User;
use Auth;

/**
 * Class UserRolesValidationRequest
 * @package App\Http\Requests
 */
class UserRolesPolicy
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var array|null
     */
    private $roles = null;

    /**
     * UserRolesPolicy constructor.
     * @param  User|null $user
     */
    public function __construct(User $user = null)
    {
        // Laravel injects $user property as new user. In this case we should
        // treat it as not configured value.
        if ($user == null || !$user->exists) {
            $this->user = Auth::user();
            $this->id = Auth::id();
        } else {
            $this->user = $user;
            $this->id = $user->id;
        }
    }

    /**
     * @return bool
     */
    public function authorized(): bool
    {
        return Auth::check();
    }

    /**
     * Get user roles key array.
     * @return array
     */
    public function roles()
    {
        if ($this->roles == null) {
            $this->roles = $this->user->roles->map(function ($role) {
                return $role->key;
            })->toArray();
        }

        return $this->roles;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = $this->roles();

        // Allow super admin do anything
        if (in_array(UserRole::SUPER_ADMIN, $roles)) return true;

        if (in_array($role, $roles)) return true;

        return false;
    }

    /**
     * @param array $searchFor
     * @return bool
     */
    public function hasAnyRole($searchFor = [])
    {
        foreach ($searchFor as $role)
            if ($this->hasRole($role)) return true;

        return false;
    }
}
