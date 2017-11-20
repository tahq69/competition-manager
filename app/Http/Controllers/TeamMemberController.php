<?php namespace App\Http\Controllers;

use App\Contracts\ITeamMemberRepository;
use App\Contracts\ITeamRepository;
use App\Http\Requests;
use App\Team;
use App\TeamMember;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamMemberController
 * @package App\Http\Controllers
 */
class TeamMemberController extends Controller
{
    /**
     * @var ITeamRepository
     */
    private $teams;

    /**
     * @var ITeamMemberRepository
     */
    private $members;

    /**
     * TeamController constructor.
     * @param ITeamRepository $teams
     * @param ITeamMemberRepository $members
     */
    public function __construct(
        ITeamRepository $teams, ITeamMemberRepository $members)
    {
        $this->middleware('auth:api');
        $this->teams = $teams;
        $this->members = $members;
    }

    /**
     * Get list of team members.
     * @param  int $teamId
     * @param  Requests\TeamMembers\Index $request
     * @return JsonResponse
     */
    public function index(
        int $teamId, Requests\TeamMembers\Index $request): JsonResponse
    {
        $orderingMapping = [
            'id' => 'id',
            'name' => 'name',
            'team_id' => 'team_id',
            'user_id' => 'user_id',
            'membership_type' => 'membership_type',
        ];

        $team = $this->teams->find($teamId, ['id']);
        $members = $this->members
            ->setupOrdering($request, $orderingMapping)
            ->filterByTeam($team->id)
            ->paginate($request->per_page ?: 15, [], [
                'id', 'name', 'team_id', 'user_id', 'membership_type'
            ]);

        return new JsonResponse($members);
    }

    /**
     * Get single team member instance.
     * @param  int $teamId
     * @param  int $memberId
     * @return JsonResponse
     */
    public function show(int $teamId, int $memberId): JsonResponse
    {
        $team = $this->members->find($memberId);

        return new JsonResponse($team);
    }

    /**
     * Store new instance of team member.
     * @param  int $teamId
     * @param  Requests\TeamMembers\Store $request
     * @return JsonResponse
     */
    public function store(
        int $teamId, Requests\TeamMembers\Store $request): JsonResponse
    {
        /** @var Team $team */
        $team = $this->teams->find($teamId);
        $details = $request->only(['user_id', 'name']);

        $member = array_key_exists('user_id', $details) && $details['user_id'] ?
            $this->inviteMember($team, $details, $request->user()->id) :
            $this->createMember($team, $details);

        return new JsonResponse($member);
    }

    /**
     * Update existing instance of team member.
     * @param  int $teamId
     * @param  int $id
     * @param  Requests\TeamMembers\Update $request
     * @return JsonResponse
     */
    public function update(
        int $teamId, int $id,
        Requests\TeamMembers\Update $request): JsonResponse
    {
        $member = $this->members->find($id);
        $details = $request->only(['user_id', 'name']);

        if (
            array_key_exists('user_id', $details) &&
            $member->user_id != $details['user_id']) {

            $team = $this->teams->find($teamId);
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