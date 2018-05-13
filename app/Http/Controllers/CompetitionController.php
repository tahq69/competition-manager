<?php namespace App\Http\Controllers;

use App\Contracts\ICompetitionRepository;
use App\Contracts\ITeamRepository;
use App\Http\Requests\Competition\Index;
use App\Http\Requests\Competition\Store;
use App\Http\Requests\Competition\Update;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * Class CompetitionController
 *
 * @package App\Http\Controllers
 */
class CompetitionController extends Controller
{
    /**
     * @var \App\Contracts\ICompetitionRepository
     */
    private $competitions;

    /**
     * @var \App\Contracts\ITeamRepository
     */
    private $teams;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICompetitionRepository $competitions
     * @param \App\Contracts\ITeamRepository        $teams
     */
    public function __construct(
        ICompetitionRepository $competitions,
        ITeamRepository $teams)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->competitions = $competitions;
        $this->teams = $teams;
    }

    /**
     * Display a listing of the competitions.
     *
     * @param \App\Http\Requests\Competition\Index $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Index $request): JsonResponse
    {
        $orderingMapping = [
            'id' => 'id',
            'title' => 'title',
            'subtitle' => 'subtitle',
            'judge_name' => 'judge_name',
            'organization_date' => 'organization_date',
        ];

        if ($request->owned) {
            $this->competitions->filterOwnedOrManaged();
        }

        if ($request->team_id) {
            $this->competitions->filterByTeam($request->team_id);
        }

        $competitions = $this->competitions
            ->setupOrdering($request, $orderingMapping)
            ->paginate($request->per_page ?: 15, [], [
                'id', 'title', 'subtitle', 'judge_id', 'judge_name',
                'organization_date', 'team_id',
            ]);

        return new JsonResponse($competitions);
    }

    /**
     * Store a newly created competition in storage.
     *
     * @param \App\Http\Requests\Competition\Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        $validatedInput = array_keys($request->rules());
        $details = $request->only($validatedInput);

        /** @var \App\Team $team */
        $team = $this->teams->find($request->team_id, ['id', 'name', 'short']);

        $details['team_name'] = $team->name;
        $details['team_short'] = $team->short;
        $details['registration_till'] = new Carbon($details['registration_till']);
        $details['organization_date'] = new Carbon($details['organization_date']);

        $competition = $this->competitions->create($details);

        return new JsonResponse($competition);
    }

    /**
     * Display the specified competition.
     *
     * @param int $id Competition identifier.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $competition = $this->competitions->find($id);

        return new JsonResponse($competition);
    }

    /**
     * Update the specified competition in storage.
     *
     * @param \App\Http\Requests\Competition\Update $request
     * @param int                                   $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $validatedInput = array_keys($request->rules());
        $details = $request->only($validatedInput);
        $details['registration_till'] = new Carbon($details['registration_till']);
        $details['organization_date'] = new Carbon($details['organization_date']);

        $competition = $request->find('competition');

        $this->competitions->update($details, $id, $competition);

        return new JsonResponse($competition);
    }
}
