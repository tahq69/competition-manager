<?php namespace Tests;

use App\Competition;
use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Discipline;
use App\Role;
use App\TeamMember;
use App\User;
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
     * @return User
     */
    protected function createSuperAdmin()
    {
        $user = factory(User::class)->states('super_admin')->create();
        $this->syncRole($user, UserRole::SUPER_ADMIN);

        return $user;
    }

    /**
     * @return User
     */
    protected function createPostManager()
    {
        $user = factory(User::class)->states('post_manager')->create();
        $this->syncRole($user, UserRole::MANAGE_POSTS);

        return $user;
    }

    /**
     * @return User
     */
    protected function createTeamOwner()
    {
        $user = factory(User::class)->states('team_owner')->create();
        $this->syncRole($user, MemberRole::MANAGE_TEAMS);

        return $user;
    }

    /**
     * @return User
     */
    protected function createTeamManager()
    {
        $user = factory(User::class)->states('team_owner')->create();
        $this->syncRoles($user, [UserRole::CREATE_TEAMS]);

        return $user;
    }

    protected function createTeamMemberManager(int $teamId, $userId = null)
    {
        $member = factory(TeamMember::class)
            ->create(['team_id' => $teamId, 'user_id' => $userId]);

        $this->syncMemberRoles($member, [
            MemberRole::MANAGE_TEAMS,
            MemberRole::MANAGE_MEMBERS,
            MemberRole::MANAGE_MEMBER_ROLES,
        ]);

        return $member;
    }

    /**
     * @param array $users
     * @return \App\Team
     */
    protected function createTeam(array $users)
    {
        $team = factory(\App\Team::class)->create();

        foreach ($users as $user) {
            $manager = factory(TeamMember::class)->create([
                'team_id' => $team->id,
                'membership_type' => TeamMember::MANAGER,
                'user_id' => $user->id,
            ]);

            $this->syncManagerRole($manager, MemberRole::MANAGE_COMPETITIONS);
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

    private function syncRole(User $user, string $role)
    {
        $user->roles()->sync([$this->findRoleId($role)]);
    }

    private function syncRoles(User $user, array $roles)
    {
        $dbRoles = collect($roles)->map(function ($role) {
            return $this->findRoleId($role);
        })->toArray();

        $user->roles()->sync($dbRoles);
    }

    private function syncMemberRoles(TeamMember $member, array $roles)
    {
        $dbRoles = collect($roles)->map(function ($role) {
            return $this->findRoleId($role);
        })->toArray();

        $member->roles()->sync($dbRoles);
    }

    private function syncManagerRole(TeamMember $manager, string $role)
    {
        $manager->roles()->sync([$this->findRoleId($role)]);
    }

    private function seedRoles()
    {
        if ($this->rolesSeeded) return;

        $this->seed('RolesTableSeeder');

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
