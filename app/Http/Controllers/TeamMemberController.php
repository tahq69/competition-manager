<?php namespace App\Http\Controllers;

use App\Contracts\ITeamMemberRepository;
use App\Contracts\ITeamRepository;
use App\Http\Requests\TeamMembers\Index;
use App\Http\Requests\TeamMembers\Store;
use App\Http\Requests\TeamMembers\Update;
use App\Team;
use App\TeamMember;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamMemberController
 *
 * @package App\Http\Controllers
 */
class TeamMemberController extends Controller
{
    /**
     * @var \App\Contracts\ITeamRepository
     */
    private $teams;

    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * TeamController constructor.
     *
     * @param \App\Contracts\ITeamRepository       $teams
     * @param \App\Contracts\ITeamMemberRepository $members
     */
    public function __construct(
        ITeamRepository $teams,
        ITeamMemberRepository $members)
    {
        $this->middleware('auth:api')
            ->except('index');

        $this->teams = $teams;
        $this->members = $members;
    }

    /**
     * Get list of team members.
     *
     * @param \App\Http\Requests\TeamMembers\Index $request
     * @param int                                  $teamId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function index(Index $request, int $teamId): JsonResponse
    {
        $orderingMapping = [
            'id' => 'id',
            'name' => 'name',
            'team_id' => 'team_id',
            'user_id' => 'user_id',
            'membership_type' => 'membership_type',
        ];

        /** @var \App\Team $team */
        $team = $request->find('team');
        $members = $this->members
            ->setupOrdering($request, $orderingMapping)
            ->filterByTeam($team->id)
            ->paginate($request->per_page ?: 15, [], [
                'id', 'name', 'team_id', 'user_id', 'membership_type',
            ]);

        return new JsonResponse($members);
    }

    /**
     * Get single team member instance.
     *
     * @param int $teamId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $teamId, int $id): JsonResponse
    {
        $team = $this->members->find($id);

        return new JsonResponse($team);
    }

    /**
     * Store new instance of team member.
     *
     * @param \App\Http\Requests\TeamMembers\Store $request
     * @param int                                  $teamId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function store(Store $request, int $teamId): JsonResponse
    {
        /** @var Team $team */
        $team = $request->find('team');
        $details = $request->only(['user_id', 'name']);
        $details['user_id'] = $details['user_id'] > 0 ? $details['user_id'] : null;

        $member = $details['user_id'] > 0 ?
            $this->inviteMember($team, $details, $request->user()->id) :
            $this->createMember($team, $details);

        return new JsonResponse($member);
    }

    /**
     * Update existing instance of team member.
     *
     * @param \App\Http\Requests\TeamMembers\Update $request
     * @param int                                   $teamId
     * @param int                                   $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(Update $request, int $teamId, int $id): JsonResponse
    {
        $member = $request->find('member');
        $details = $request->only(['user_id', 'name']);
        $details['user_id'] = $details['user_id'] > 0 ? $details['user_id'] : null;

        if (
            array_key_exists('user_id', $details) &&
            $member->user_id != $details['user_id']
        ) {
            $team = $request->find('team');
            $details['membership_type'] = TeamMember::INVITED;

            /* TODO: implement messaging service
            $this->messaging->dismissTeamMemberInvitation(
                $details['user_id'], $team->id
            );

            $this->messaging->sendTeamMemberInvitation(
                $request->user()->id, $details['user_id'], $team->name,
                $member->id
            ); */
        }

        $this->members->update($details, $id, $member);

        return new JsonResponse($member);
    }

    private function inviteMember(
        Team $team, array $details, int $managerId): TeamMember
    {
        $details['membership_type'] = TeamMember::INVITED;
        $member = $this->teams->createMember($team, $details);

        /* TODO: implement messaging service
        $this->messaging->sendTeamMemberInvitation(
            $managerId, $details['user_id'], $team->name, $member->id
        ); */

        return $member;
    }

    private function createMember(Team $team, array $details): TeamMember
    {
        $details['user_id'] = null;
        $details['membership_type'] = TeamMember::MEMBER;
        $member = $this->teams->createMember($team, $details);

        return $member;
    }
}
