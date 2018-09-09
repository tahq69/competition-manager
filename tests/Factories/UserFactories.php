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
    protected function createUser(array $attributes = []): \App\User
    {
        factory(User::class)->create();
        return factory(User::class)->create($attributes);
    }

    protected function createSuperAdmin(): \App\User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::SUPER_ADMIN);
    }

    protected function createPostManager(): \App\User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::MANAGE_POSTS);
    }

    protected function createTeamOwner(): \App\User
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::CREATE_TEAMS);
    }
}
