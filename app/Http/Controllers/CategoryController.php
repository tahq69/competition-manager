<?php namespace App\Http\Controllers;

use App\CategoryGroup;
use App\Discipline;

use App\Contracts\ICategoryGroupRepository as IGroups;
use App\Contracts\ICategoryRepository as ICategories;
use App\Contracts\IDisciplineRepository as IDisciplines;

use App\Http\Requests\Category\Destroy as DestroyRequest;
use App\Http\Requests\Category\Store as StoreRequest;
use App\Http\Requests\Category\Update as UpdateRequest;

use Illuminate\Http\JsonResponse;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var ICategories
     */
    private $categories;

    /**
     * @var IDisciplines
     */
    private $disciplines;

    /**
     * @var IGroups
     */
    private $groups;

    /**
     * CompetitionController constructor.
     * @param ICategories $categories
     * @param IDisciplines $disciplines
     * @param IGroups $groups
     */
    public function __construct(
        ICategories $categories,
        IDisciplines $disciplines,
        IGroups $groups)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->categories = $categories;
        $this->disciplines = $disciplines;
        $this->groups = $groups;
    }

    /**
     * Display a listing of the resource.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @return JsonResponse
     */
    public function index(
        int $competitionId,
        int $disciplineId,
        int $groupId): JsonResponse
    {
        $categories = $this->categories
            ->whereGroup($groupId)
            ->sortByOrder()
            ->get([], [
                'area_id', 'category_group_id', 'competition_id',
                'discipline_id', 'display_type', 'id', 'max', 'min', 'order',
                'short', 'title', 'type',
            ]);

        return new JsonResponse($categories->toArray());
    }

    /**
     * Store new record of resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @param  StoreRequest $request
     * @return JsonResponse
     */
    public function store(
        int $competitionId,
        int $disciplineId,
        int $groupId,
        StoreRequest $request): JsonResponse
    {
        $details = $request->only([
            'area_id', 'category_group_id', 'competition_id', 'discipline_id',
            'display_type', 'max', 'min', 'short', 'title',
        ]);

        /** @var Discipline $discipline */
        $discipline = $this->disciplines->find($disciplineId);
        /** @var CategoryGroup $group */
        $group = $this->groups->find($groupId);
        $categoryCount = $this->categories->whereGroup($groupId)->count();

        $details['order'] = $categoryCount + 1;
        $details['type'] = $discipline->category_type;
        $details['discipline_title'] = $discipline->title;
        $details['discipline_short'] = $discipline->short;
        $details['category_group_title'] = $group->title;
        $details['category_group_short'] = $group->short;

        $category = $this->categories->create($details);

        return new JsonResponse($category);
    }

    /**
     * Get single resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @param  int $id
     * @return JsonResponse
     */
    public function show(
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id): JsonResponse
    {
        $category = $this->categories->find($id);

        return new JsonResponse($category);
    }

    /**
     * Update existing resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @param  int $id
     * @param  UpdateRequest $request
     * @return JsonResponse
     */
    public function update(
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id,
        UpdateRequest $request): JsonResponse
    {
        $category = $this->categories->find($id);

        $details = $request->only([
            'area_id', 'display_type', 'max', 'min', 'short', 'title',
        ]);

        $this->categories->update($details, $id, $category);

        return new JsonResponse($category);
    }

    /**
     * Delete existing resource instance.
     * @param  int $competitionId
     * @param  int $disciplineId
     * @param  int $groupId
     * @param  int $id
     * @param  DestroyRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id,
        DestroyRequest $request): JsonResponse
    {
        $this->categories->find($id)->delete();

        return new JsonResponse(true);
    }
}
