<?php namespace App\Http\Controllers;

use App\Contracts\ICompetitionRepository as Competitions;
use App\Contracts\IUserRepository as Users;
use App\Http\Requests\Competition\Index as IndexRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class CompetitionController
 * @package App\Http\Controllers
 */
class CompetitionController extends Controller
{
    /**
     * @var Competitions
     */
    private $competitions;

    /**
     * @var Users
     */
    private $users;

    /**
     * CompetitionController constructor.
     * @param Competitions $competitions
     * @param Users $users
     */
    public function __construct(Competitions $competitions, Users $users)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->competitions = $competitions;
        $this->users = $users;
    }

    /**
     * Display a listing of the resource.
     * @param  IndexRequest $request
     * @return JsonResponse
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
     * Get single competition instance.
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $competition = $this->competitions->find($id);

        return new JsonResponse($competition);
    }
}
