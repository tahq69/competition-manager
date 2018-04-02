<?php namespace App\Repositories;

use App\Contracts\IRoleRepository as IRoles;
use App\Contracts\ITeamMemberRepository;
use App\TeamMember;

/**
 * Class TeamMemberRepository
 * @package App\Repositories
 */
class TeamMemberRepository
    extends PaginationRepository
    implements ITeamMemberRepository
{
    /**
     * @var \App\Contracts\IRoleRepository
     */
    private $roles;

    /**
     * TeamMemberRepository constructor.
     * @param \App\Contracts\IRoleRepository $roles
     */
    public function __construct(IRoles $roles)
    {
        parent::__construct();

        $this->roles = $roles;
    }

    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return TeamMember::class;
    }

    /**
     * Set team id filter on queryable
     * @param int $id
     * @return $this
     */
    public function filterByTeam(int $id)
    {
        return $this->setWhere('team_id', $id);
    }

    /**
     * Set user id filter on queryable.
     * @param int $id
     * @return $this
     */
    public function filterByUser(int $id)
    {
        return $this->setWhere('user_id', $id);
    }

    /**
     * Set membership type filter on queryable.
     * @param string $type
     * @return $this
     */
    public function filterByMembership(string $type)
    {
        return $this->setWhere('membership_type', $type);
    }

    /**
     * Join user details to member
     * @return $this
     */
    public function withUserDetails()
    {
        return $this->with('user');
    }

    /**
     * Join team member roles.
     * @return $this
     */
    public function withTeamMemberRoles()
    {
        return $this->with('roles');
    }

    /**
     * Synchronize member roles with provided array role keys.
     * @param  int $memberId
     * @param  array $roles
     * @return $this
     */
    public function sycnRoles(int $memberId, array $roles)
    {
        /** @var \App\TeamMember $member */
        $member = $this->find($memberId);
        $dbRoles = $this->roles->whereKeyIn($roles)->get(['id', 'key']);

        $member->roles()->sync($dbRoles);

        return $this;
    }
}
