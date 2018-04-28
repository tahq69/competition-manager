<?php namespace App\Contracts;

/**
 * Class UserRole
 * @package App\Contracts
 */
class UserRole
{
    // Can do any action in system
    const SUPER_ADMIN = 'SUPER_ADMIN';

    // Can create/manage own posts
    const CREATE_POST = 'CREATE_POST';
    const MANAGE_POSTS = 'MANAGE_POSTS';

    // Can manage users
    const MANAGE_USERS = 'MANAGE_USERS';
    const MANAGE_USER_ROLES = 'MANAGE_USER_ROLES';

    // Can create/manage teams
    const CREATE_TEAMS = 'CREATE_TEAMS';
}
