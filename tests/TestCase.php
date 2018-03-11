<?php namespace Tests;

use App\CategoryGroup;
use App\Competition;
use App\Discipline;
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
     * @param \Illuminate\Foundation\Testing\TestResponse $response
     * @param int $count
     */
    protected function assertJsonCount($response, int $count)
    {
        $this->assertTrue(
            count($response->json()) == $count,
            'Response record count is not equal to ' . $count
        );
    }

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
     * @return \App\User
     */
    protected function createTeamOwner()
    {
        $user = factory(\App\User::class)->states('team_owner')->create();
        $this->syncRole($user, \App\Role::MANAGE_TEAMS);

        return $user;
    }

    /**
     * @return \App\User
     */
    protected function createTeamManager()
    {
        $user = factory(\App\User::class)->states('team_owner')->create();
        $this->syncRoles($user, [
            \App\Role::MANAGE_TEAMS,
            \App\Role::CREATE_TEAMS
        ]);

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

    protected function createDisciplines($count = 1, $attributes = [])
    {
        // Create 3 competitions and get only second one.
        $cm = $this->makeSurrounded(Competition::class);
        // Make extra discipline for first cm.
        factory(Discipline::class)->create(['competition_id' => $cm->id - 1]);
        $result = factory(Discipline::class, $count)->create(
            array_merge(['competition_id' => $cm->id], $attributes)
        );
        // Make extra discipline for last cm.
        factory(Discipline::class)->create(['competition_id' => $cm->id + 1]);

        return $result;
    }

    private function syncRole(\App\User $user, string $role)
    {
        $user->roles()->sync([$this->findRoleId($role)]);
    }

    private function syncRoles(\App\User $user, array $roles)
    {
        $dbRoles = collect($roles)->map(function ($role) {
            return $this->findRoleId($role);
        })->toArray();

        $user->roles()->sync($dbRoles);
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

    private function makeSurrounded($className, $count = 1, $attributes = [])
    {
        $competitions = factory($className, 2 + $count)->create($attributes);
        $records = collect($competitions)->slice(1, $count);
        if ($count == 1) return $records->first();
        return $records->values()->all();
    }
}
