<?php namespace Tests;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\TeamMember;
use App\User;

/**
 * Trait UserFactories
 * @package Tests
 */
trait UserFactories
{
    protected function createUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    protected function createSuperAdmin(): User
    {
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::SUPER_ADMIN);
    }

    protected function createPostManager(): User
    {
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::MANAGE_POSTS);
    }

    protected function createTeamOwner(): User
    {
        $user = factory(User::class)->create();

        return RoleHelper::userSync($user, UserRole::CREATE_TEAMS);
    }
}
