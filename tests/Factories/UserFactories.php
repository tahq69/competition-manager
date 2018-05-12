<?php namespace Tests\Factories;

use App\Contracts\UserRole;
use App\User;
use Tests\RoleHelper;

/**
 * Trait UserFactories
 *
 * @package Tests
 */
trait UserFactories
{
    protected function createUser(array $attributes = [])
    {
        factory(User::class)->create();
        return factory(User::class)->create($attributes);
    }

    protected function createSuperAdmin(): User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::SUPER_ADMIN);
    }

    protected function createPostManager(): User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::MANAGE_POSTS);
    }

    protected function createTeamOwner(): User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::CREATE_TEAMS);
    }
}
