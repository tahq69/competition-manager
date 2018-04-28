<?php

use App\Contracts\UserRole;
use App\Role;
use Illuminate\Database\Seeder;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $roles;

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $this->createAdmin();
        $this->createPostCreator();
        $this->createPostManager();
        $this->createTeamManager();

        // Create extra 10 random users
        factory(App\User::class, 20)->create();
    }

    private function createAdmin()
    {
        $admin = factory(\App\User::class)->states('super_admin')->create();

        // add super admin role for admin user
        $admin->roles()->sync([$this->roleId(UserRole::SUPER_ADMIN)]);

        factory(App\Post::class, 5)->create(['author_id' => $admin->id]);
    }

    private function createPostCreator()
    {
        $user = factory(\App\User::class)->states('post_creator')->create();

        // add create post role for user
        $user->roles()->sync([$this->roleId(UserRole::CREATE_POST)]);

        factory(App\Post::class, 10)->create(['author_id' => $user->id]);
    }

    private function createPostManager()
    {
        $user = factory(\App\User::class)->states('post_manager')->create();

        // add manage posts role for user
        $user->roles()->sync([$this->roleId(UserRole::MANAGE_POSTS)]);
    }

    private function createTeamManager()
    {
        $user = factory(\App\User::class)->states('team_owner')->create();

        $user->roles()->sync([$this->roleId(UserRole::CREATE_TEAMS)]);

        // add teams for manager
        factory(\App\Team::class, 5)->create([
            'created_by' => $user->id,
            'created_by_name' => $user->name,
        ])->each(function ($team) use ($user) {
            // Associate manager with created team
            $manager = factory(\App\TeamMember::class)->create([
                'team_id' => $team->id,
                'user_id' => $user->id
            ]);

            // Create members in team
            factory(\App\TeamMember::class, 10)->create(['team_id' => $team->id]);

            // Create additional managers for team
            factory(\App\TeamMember::class, 2)->create([
                'team_id' => $team->id,
                'membership_type' => \App\TeamMember::MANAGER,
                'user_id' => function () {
                    return factory(\App\User::class)->create()->id;
                }
            ]);

            // Create competitions for this team
            factory(\App\Competition::class, 12)->create(['team_id' => $team->id]);

            // Assign member roles to manager
            $manager->roles()->sync([
                $this->roleId(\App\Contracts\MemberRole::MANAGE_TEAMS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_MEMBERS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_MEMBER_ROLES),
                $this->roleId(\App\Contracts\MemberRole::CREATE_COMPETITIONS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_COMPETITIONS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_COMPETITION_AREAS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_COMPETITION_DISCIPLINES),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_COMPETITION_MEMBERS),
                $this->roleId(\App\Contracts\MemberRole::MANAGE_COMPETITION_JUDGES),
            ]);
        });
    }

    private function roleId($roleKey)
    {
        if (!$this->roles) {
            $roleTable = app(Role::class)->getTable();
            $this->roles = DB::table($roleTable)->get();
        }

        foreach ($this->roles as $role) {
            if ($role->key == $roleKey)
                return $role->id;
        }

        return 0;
    }
}
