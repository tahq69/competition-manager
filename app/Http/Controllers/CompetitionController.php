<?php namespace App\Http\Controllers;

use App\Contracts\ICompetitionRepository as Competitions;
use App\Contracts\IUserRepository as Users;
use App\Http\Requests\Competition\Index as IndexRequest;
use App\Http\Requests\Competition\Store as StoreRequest;
use App\Http\Requests\Competition\Update as UpdateRequest;
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
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICompetitionRepository $competitions
     * @param \App\Contracts\IUserRepository        $users
     */
    public function __construct(Competitions $competitions, Users $users)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->competitions = $competitions;
        $this->users = $users;
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
        //
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
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        //
    }
}
