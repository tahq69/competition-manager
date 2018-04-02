<?php namespace App\Http\Controllers;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\Http\Requests\TeamMemberRoles\Index as IndexRequest;
use App\Http\Requests\TeamMemberRoles\Store as StoreRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamMemberRoleController
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
     * @param \App\Contracts\ITeamMemberRepository $members
     */
    public function __construct(IMembers $members)
    {
        $this->middleware('auth:api');
        $this->members = $members;
    }

    /**
     * @param  int $teamId
     * @param  int $memberId
     * @param  \App\Http\Requests\TeamMemberRoles\Index $request
     * @return JsonResponse
     */
    public function index(
        int $teamId,
        int $memberId,
        IndexRequest $request): JsonResponse
    {
        /** @var \App\TeamMember $member */
        $member = $this->members
            ->withTeamMemberRoles()
            ->find($memberId, ['id', 'team_id']);

        $roles = $member->roles->map(function (\App\Role $role) {
            return $role->key;
        });

        return new JsonResponse($roles);
    }

    /**
     * @param  int $teamId
     * @param  int $memberId
     * @param  \App\Http\Requests\TeamMemberRoles\Store $request
     * @return JsonResponse
     */
    public function store(
        int $teamId,
        int $memberId,
        StoreRequest $request): JsonResponse
    {
        $this->members->sycnRoles($memberId, $request->roles);

        return new JsonResponse(true);
    }
}
