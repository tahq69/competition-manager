<?php namespace Tests;

use App\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $roles;

    /**
     * @var boolean
     */
    private $rolesSeeded = false;

    /**
     * @return \App\User
     */
    protected function createSuperAdmin()
    {
        $user = factory(\App\User::class)->states('super_admin')->create();
        $this->syncRole($user, \App\Role::SUPER_ADMIN);

        return $user;
    }

    /**
     * @return \App\User
     */
    protected function createPostManager()
    {
        $user = factory(\App\User::class)->states('post_manager')->create();
        $this->syncRole($user, \App\Role::MANAGE_POSTS);

        return $user;
    }

    /**
     * @param array $users
     * @return \App\Team
     */
    protected function createTeam(array $users)
    {
        $team = factory(\App\Team::class)->create();

        foreach ($users as $user) {
            $manager = factory(\App\TeamMember::class)->create([
                'team_id' => $team->id,
                'membership_type' => \App\TeamMember::MANAGER,
                'user_id' => $user->id,
            ]);

            $this->syncManagerRole($manager, Role::MANAGE_COMPETITIONS);
        }

        return $team;
    }

    private function syncRole(\App\User $user, string $role)
    {
        $user->roles()->sync([$this->findRoleId($role)]);
    }

    private function syncManagerRole(\App\TeamMember $manager, string $role)
    {
        $manager->roles()->sync([$this->findRoleId($role)]);
    }

    private function seedRoles()
    {
        if ($this->rolesSeeded) return;

        foreach (Role::ALL_ROLES as $role) {
            (new Role(['key' => $role]))->save();
        }

        $this->rolesSeeded = true;
    }

    private function findRoleId($roleKey)
    {
        $this->seedRoles();

        if (!$this->roles) {
            $this->roles = Role::all();
        }

        foreach ($this->roles as $role) {
            if ($role->key == $roleKey)
                return $role->id;
        }

        return 0;
    }
}
