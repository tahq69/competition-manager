<?php namespace App\Contracts;

/**
 * Class MemberRole
 * @package App\Contracts
 */
class MemberRole
{
    // Team specific info management.
    const MANAGE_TEAMS = 'MANAGE_TEAMS';
    const MANAGE_MEMBERS = 'MANAGE_MEMBERS';
    const MANAGE_MEMBER_ROLES = 'MANAGE_MEMBER_ROLES';

    // Team specific competition info management.
    const CREATE_COMPETITIONS = 'CREATE_COMPETITIONS';
    const MANAGE_COMPETITIONS = 'MANAGE_COMPETITIONS';
    const MANAGE_COMPETITION_AREAS = 'MANAGE_COMPETITION_AREAS';
    const MANAGE_COMPETITION_DISCIPLINES = 'MANAGE_COMPETITION_DISCIPLINES';
    const MANAGE_COMPETITION_MEMBERS = 'MANAGE_COMPETITION_MEMBERS';
    const MANAGE_COMPETITION_JUDGES = 'MANAGE_COMPETITION_JUDGES';
}
