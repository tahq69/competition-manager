<?php namespace App\Http\Controllers;

use App\Contracts\ICategoryGroupRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class CategoryGroupController
 * @package App\Http\Controllers
 */
class CategoryGroupController extends Controller
{
    /**
     * @var ICategoryGroupRepository
     */
    private $groups;

    /**
     * CompetitionController constructor.
     * @param ICategoryGroupRepository $groups
     */
    public function __construct(ICategoryGroupRepository $groups)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->groups = $groups;
    }

    /**
     * Display a listing of the resource.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @return JsonResponse
     */
    public function index(int $competitionId, int $disciplineId): JsonResponse
    {
        $groups = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->sortByOrder()
            ->get([], [
                'competition_id', 'discipline_id', 'order', 'short', 'title',
                'id',
            ]);

        return new JsonResponse($groups->toArray());
    }

    /**
     * Get single resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $competitionId, int $disciplineId, int $id):
    JsonResponse
    {
        $group = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->find($id);

        return new JsonResponse($group);
    }
}