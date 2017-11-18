<?php namespace App\Http\Controllers;

use App\Contracts\ITeamRepository;
use App\Http\Requests;
use App\Role;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamController
 * @package App\Http\Controllers
 */
class TeamController extends Controller
{
    /**
     * @var ITeamRepository
     */
    private $teams;

    /**
     * TeamController constructor.
     * @param ITeamRepository $teams
     */
    public function __construct(ITeamRepository $teams)
    {
        $this->middleware('auth:api');
        $this->teams = $teams;
    }

    /**
     * Get list of teams.
     * @param  \App\Http\Requests\Team\ViewList $request
     * @return JsonResponse
     */
    public function index(Requests\Team\ViewList $request): JsonResponse
    {
        // If user is not a super admin, allow see only managed teams
        if (!$request->user()->hasRole(Role::SUPER_ADMIN)) {
            $this->teams->filterByManager($request->user()->id);
        }

        $orderingMapping = [
            'id' => 'teams.id',
            'name' => 'name',
            'short' => 'short',
            'created_at' => 'teams.created_at',
        ];

        $teams = $this->teams
            ->setupOrdering($request, $orderingMapping)
            ->paginate($request->per_page ?: 15, [], [
                'teams.id', 'name', 'short', 'teams.created_at',
            ]);

        return new JsonResponse($teams);
    }

    /**
     * Save new team to database and attach creator as owner of the team.
     * POST    /api/admin/teams
     * @param  AdminStoreTeam $request
     * @return JsonResponse

    public function store(AdminStoreTeam $request)
     * {
     * $this->authorize('create', Team::class);
     * $authUserId = $request->user()->id;
     * $details = $request->only(['name', 'short', 'logo']);
     *
     * try {
     * $team = $this->teams->createAndAttachOwner($details, $authUserId);
     * } catch (Exception $exception) {
     * return new JsonResponse($exception->getMessage(), 507);
     * }
     *
     * return new JsonResponse($team);
     * }*/

    /**
     * Get single team instance.
     * @param  int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $team = $this->teams->find($id);

        return new JsonResponse($team);
    }

    /**
     * Update existing team details.
     * PUT/PATCH /api/admin/teams/{team}
     * @param    AdminUpdateTeam $request
     * @param    int $teamId
     * @return   JsonResponse

    public function update(AdminUpdateTeam $request, $teamId)
     * {
     * $team = $this->teams->find($teamId);
     *
     * $this->authorize('update', $team);
     *
     * $details = $request->only(['name', 'short', 'logo']);
     *
     * $this->teams->update($details, $teamId, $team);
     *
     * return new JsonResponse($team);
     * }*/
}
