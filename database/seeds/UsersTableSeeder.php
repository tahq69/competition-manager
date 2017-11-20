<?php

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

    const POST_MANAGER_EMAIL = 'post.manager@crip.lv';
    const TEAM_MANAGER_EMAIL = 'team.manager@crip.lv';
    const JUDGE_EMAIL = 'judge@crip.lv';

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
        $this->createJudge();

        // Create extra 50 random users
        factory(App\User::class, 20)->create()->each(function ($user) {
            // Create random user posts
            factory(\App\Post::class, 3)->create(['author_id' => $user->id]);
        });
    }

    private function createAdmin()
    {
        $admin = factory(\App\User::class)->states('super_admin')->create();

        // add super admin role for admin user
        $admin->roles()->sync([$this->roleId(Role::SUPER_ADMIN)]);

        factory(App\Post::class, 5)->create(['author_id' => $admin->id]);
    }

    private function createPostCreator()
    {
        $user = factory(\App\User::class)->states('post_manager')->create();

        // add create post role for user
        $user->roles()->sync([$this->roleId(Role::CREATE_POST)]);

        factory(App\Post::class, 20)->create(['author_id' => $user->id]);
    }

    private function createPostManager()
    {
        $user = App\User::create([
            'name' => 'post.manager',
            'email' => static::POST_MANAGER_EMAIL,
            'password' => bcrypt('password')
        ]);

        // add manage posts role for user
        $user->roles()->sync([$this->roleId(Role::MANAGE_POSTS)]);
    }

    private function createTeamManager()
    {
        $user = App\User::create([
            'name' => 'team.manager',
            'email' => static::TEAM_MANAGER_EMAIL,
            'password' => bcrypt('password')
        ]);

        $user->roles()->sync([$this->roleId(Role::CREATE_TEAMS)]);
    }

    private function createJudge()
    {
        $user = App\User::create([
            'name' => 'judge',
            'email' => static::JUDGE_EMAIL,
            'password' => bcrypt('password')
        ]);

        $user->roles()->sync([
            $this->roleId(Role::CREATE_COMPETITIONS),
            $this->roleId(Role::EDIT_COMPETITIONS),
        ]);
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