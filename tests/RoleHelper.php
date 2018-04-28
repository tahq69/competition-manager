<?php namespace Tests;

use App\Role;
use App\TeamMember;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class RoleHelper
 * @package Tests
 */
class RoleHelper
{
    /**
     * @var array Database role map with key of role string and id as value.
     */
    private static $roles = [];

    /**
     * Get role database identifier by key string value.
     * @param  string $roleKey Required role key string value.
     * @return int
     */
    public static function getId(string $roleKey): int
    {
        self::ensueRolesLoaded();

        return self::$roles[$roleKey];
    }

    /**
     * Synchronize user roles.
     * @param  User $user
     * @param  string $roleKey,...
     * @return User
     */
    public static function userSync(User $user, string $roleKey): User
    {
        $roles = func_get_args();

        array_shift($roles);

        self::syncRoles($user->roles(), $roles);

        return $user;
    }

    /**
     * Synchronize team member roles.
     * @param  TeamMember $member
     * @param  string $roleKey,...
     * @return TeamMember
     */
    public static function memberSync(
        TeamMember $member,
        string $roleKey
    ): TeamMember
    {
        $roles = func_get_args();

        array_shift($roles);

        self::syncRoles($member->roles(), $roles);

        return $member;
    }

    private static function ensueRolesLoaded(): void
    {
        if (self::isRolesEmpty()) {
            self::fetchRoles();
        }
    }

    private static function isRolesEmpty(): bool
    {
        return count(self::$roles) == 0;
    }

    private static function fetchRoles(): void
    {
        $roleTable = app(Role::class)->getTable();
        $roles = \DB::table($roleTable)->get();

        foreach ($roles as $role) {
            self::$roles[$role->key] = $role->id;
        }
    }

    private static function syncRoles(
        BelongsToMany $relation,
        array $roles
    ): void
    {
        self::ensueRolesLoaded();

        $roleIds = [];

        foreach ($roles as $role) {
            $roleIds[] = self::getId($role);
        }

        $relation->sync($roleIds);
    }
}
