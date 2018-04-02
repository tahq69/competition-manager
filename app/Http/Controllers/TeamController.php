<?php namespace App\Http\Controllers;

use App\Contracts\ITeamRepository;
use App\Http\Requests\Team\Index as IndexRequest;
use App\Http\Requests\Team\Store as StoreRequest;
use App\Http\Requests\Team\Update as UpdateRequest;
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
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->teams = $teams;
    }

    /**
     * Get list of teams.
     * @param  IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        if (\Auth::check()) {
            // If user is not a super admin, allow see only managed teams when
            // request flag is presented.
            $isSuperAdmin = $request->user()->hasRole(Role::SUPER_ADMIN);
            if ($request->managed && !$isSuperAdmin) {
                $this->teams->filterByManager($request->user()->id);
            }
        }

        $orderingMapping = [
            'id' => 'teams.id',
            'name' => 'teams.name',
            'short' => 'teams.short',
            'created_at' => 'teams.created_at',
        ];

        $teams = $this->teams
            ->setupOrdering($request, $orderingMapping, 'teams.id')
            ->paginate($request->per_page ?: 15, [], [
                'teams.id', 'teams.name', 'teams.short', 'teams.created_at',
                'teams.logo',
            ]);

        return new JsonResponse($teams);
    }

    /**
     * Save new team to database and attach creator as owner of the team.
     * @param  StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $user = $request->user();
        $details = $request->only(['name', 'short', 'logo',]);

        try {
            $team = $this->teams->createAndAttachManager($details, $user);
        } catch (\Exception $exception) {
            \Log::error('Could not create and attach manager', [$exception]);
            return new JsonResponse($exception->getMessage(), 507);
        }

        return new JsonResponse($team);
    }

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
     * @param  UpdateRequest $request
     * @param  int $teamId
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $teamId): JsonResponse
    {
        $team = $this->teams->find($teamId);

        $details = $request->only(['name', 'short', 'logo']);

        $this->teams->update($details, $teamId, $team);

        return new JsonResponse($team);
    }
}
