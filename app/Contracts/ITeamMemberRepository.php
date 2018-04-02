<?php namespace App\Contracts;

/**
 * Interface ITeamMemberRepository
 * @package App\Contracts
 */
interface ITeamMemberRepository extends IPaginateRepository
{
    /**
     * Set team id filter on querable.
     * @param int $id
     * @return $this
     */
    public function filterByTeam(int $id);

    /**
     * Set user id filter on querable.
     * @param int $id
     * @return $this
     */
    public function filterByUser(int $id);

    /**
     * Set membership type filter on querable.
     * @param string $type
     * @return $this
     */
    public function filterByMembership(string $type);

    /**
     * Join user details to member.
     * @return $this
     */
    public function withUserDetails();

    /**
     * Join team member roles.
     * @return $this
     */
    public function withTeamMemberRoles();
}