<?php namespace App\Http\Controllers;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\Http\Requests\TeamMemberRoles\Index;
use App\Http\Requests\TeamMemberRoles\Store;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamMemberRoleController
 *
 * @package App\Http\Controllers
 */
class TeamMemberRoleController extends Controller
{
    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * TeamMemberRoleController constructor.
     *
     * @param \App\Contracts\ITeamMemberRepository $members
     */
    public function __construct(IMembers $members)
    {
        $this->middleware('auth:api');
        $this->members = $members;
    }

    /**
     * @param \App\Http\Requests\TeamMemberRoles\Index $request
     * @param int                                      $teamId
     * @param int                                      $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Index $request, int $teamId, int $id): JsonResponse
    {
        /** @var \App\TeamMember $member */
        $member = $this->members
            ->withTeamMemberRoles()
            ->find($id, ['id', 'team_id']);

        $roles = $member->roles->map(function (\App\Role $role) {
            return $role->key;
        });

        return new JsonResponse($roles);
    }

    /**
     * @param int                                      $teamId
     * @param int                                      $id
     * @param \App\Http\Requests\TeamMemberRoles\Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request, int $teamId, int $id): JsonResponse
    {
        $this->members->sycnRoles($id, $request->roles);

        return new JsonResponse(true);
    }
}
