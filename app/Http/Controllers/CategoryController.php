<?php namespace App\Http\Controllers;

use App\Contracts\ICategoryRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var ICategoryRepository
     */
    private $categories;

    /**
     * CompetitionController constructor.
     * @param ICategoryRepository $categories
     */
    public function __construct(ICategoryRepository $categories)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->categories = $categories;
    }

    /**
     * Display a listing of the resource.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @return JsonResponse
     */
    public function index(int $competitionId, int $disciplineId, int $groupId):
    JsonResponse
    {
        $categories = $this->categories
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->whereGroup($groupId)
            ->sortByOrder()
            ->get([], [
                'competition_id', 'discipline_id', 'category_group_id',
                'area_id', 'order', 'short', 'title', 'id',
            ]);

        return new JsonResponse($categories->toArray());
    }

    /**
     * Get single resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $competitionId, int $disciplineId, int $groupId,
                         int $id): JsonResponse
    {
        $category = $this->categories
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->whereGroup($groupId)
            ->find($id);

        return new JsonResponse($category);
    }
}