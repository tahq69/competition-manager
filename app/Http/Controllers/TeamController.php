<?php namespace App\Http\Controllers;

use App\Contracts\ITeamRepository;
use App\Contracts\UserRole;
use App\Http\Requests\Team\Index;
use App\Http\Requests\Team\Store;
use App\Http\Requests\Team\Update;
use Illuminate\Http\JsonResponse;

/**
 * Class TeamController
 *
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
     *
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
     *
     * @param \App\Http\Requests\Team\Index $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Index $request): JsonResponse
    {
        if (\Auth::check()) {
            // If user is not a super admin, allow see only managed teams when
            // request flag is presented.
            $isSuperAdmin = $request->user()->hasRole(UserRole::SUPER_ADMIN);
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
     *
     * @param \App\Http\Requests\Team\Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        $user = $request->user();
        $details = $request->only(['name', 'short', 'logo',]);

        try {
            $team = $this->teams->createAndAttachManager($details, $user);
        } catch (\Exception $exception) {
            report($exception);
            return new JsonResponse($exception->getMessage(), 507);
        }

        return new JsonResponse($team);
    }

    /**
     * Get single team instance.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $team = $this->teams->find($id);

        return new JsonResponse($team);
    }

    /**
     * Update existing team details.
     *
     * @param \App\Http\Requests\Team\Update $request
     * @param int                            $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $team = $request->find('team');

        $details = $request->only(['name', 'short', 'logo']);

        $this->teams->update($details, $id, $team);

        return new JsonResponse($team);
    }
}
