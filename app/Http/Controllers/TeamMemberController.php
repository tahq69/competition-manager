<?php namespace App\Http\Controllers;

use App\Contracts\ITeamMemberRepository;
use App\Contracts\ITeamRepository;
use App\Http\Requests;
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
        ITeamRepository $teams, ITeamMemberRepository $members
    )
    {
        $this->middleware('auth:api');
        $this->teams = $teams;
        $this->members = $members;
    }

    /**
     * Get list of team members.
     * @param int $teamId
     * @param Requests\TeamMembers\ViewList $request
     * @return JsonResponse
     */
    public function index(
        int $teamId, Requests\TeamMembers\ViewList $request
    ): JsonResponse
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
}