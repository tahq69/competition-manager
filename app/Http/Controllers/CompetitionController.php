<?php namespace App\Http\Controllers;

use App\Http\Requests\Competition\Index as IndexRequest;
use App\Http\Requests\Competition\Store as StoreRequest;
use App\Http\Requests\Competition\Update as UpdateRequest;
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
     * @var \App\Contracts\IUserRepository
     */
    private $users;

    /**
     * @var \App\Contracts\ITeamRepository
     */
    private $teams;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICompetitionRepository $competitions
     * @param \App\Contracts\IUserRepository        $users
     * @param \App\Contracts\ITeamRepository        $teams
     */
    public function __construct(
        \App\Contracts\ICompetitionRepository $competitions,
        \App\Contracts\IUserRepository $users,
        \App\Contracts\ITeamRepository $teams
    )
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->competitions = $competitions;
        $this->users = $users;
        $this->teams = $teams;
    }

    /**
     * Display a listing of the competitions.
     *
     * @param \App\Http\Requests\Competition\Index $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
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
    public function store(StoreRequest $request): JsonResponse
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
     * @param int                                   $competitionId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        UpdateRequest $request,
        int $competitionId
    ): JsonResponse
    {
        $validatedInput = array_keys($request->rules());
        $details = $request->only($validatedInput);
        $details['registration_till'] = new Carbon($details['registration_till']);
        $details['organization_date'] = new Carbon($details['organization_date']);

        $competition = $this->competitions->find($competitionId);

        $this->competitions->update($details, $competitionId, $competition);

        return new JsonResponse($competition);
    }
}
