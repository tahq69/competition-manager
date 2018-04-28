<?php

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Role;
use Illuminate\Database\Seeder;

/**
 * Class RolesTableSeeder
 */
class RolesTableSeeder extends Seeder
{
    const ROLES = [
        UserRole::SUPER_ADMIN,
        UserRole::CREATE_POST,
        UserRole::MANAGE_POSTS,
        UserRole::CREATE_TEAMS,
        UserRole::MANAGE_USERS,
        UserRole::MANAGE_USER_ROLES,

        MemberRole::MANAGE_TEAMS,
        MemberRole::MANAGE_MEMBERS,
        MemberRole::MANAGE_MEMBER_ROLES,
        MemberRole::MANAGE_COMPETITIONS,
        MemberRole::CREATE_COMPETITIONS,
        MemberRole::MANAGE_COMPETITION_AREAS,
        MemberRole::MANAGE_COMPETITION_DISCIPLINES,
        MemberRole::MANAGE_COMPETITION_JUDGES,
        MemberRole::MANAGE_COMPETITION_MEMBERS,
    ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        foreach (self::ROLES as $role) {
            Role::create(['key' => $role]);
        }
    }
}
