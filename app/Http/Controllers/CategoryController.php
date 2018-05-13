<?php namespace App\Http\Controllers;

use App\Contracts\ICategoryRepository;
use App\Http\Requests\Category\Destroy;
use App\Http\Requests\Category\Store;
use App\Http\Requests\Category\Update;
use Illuminate\Http\JsonResponse;

/**
 * Class CategoryController
 *
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var \App\Contracts\ICategoryRepository
     */
    private $categories;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICategoryRepository $categories
     */
    public function __construct(ICategoryRepository $categories)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->categories = $categories;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $competitionId
     * @param int $disciplineId
     * @param int $groupId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(
        int $competitionId,
        int $disciplineId,
        int $groupId
    ): JsonResponse
    {
        $categories = $this->categories
            ->whereGroup($groupId)
            ->sortByOrder()
            ->get([
                'area_id', 'category_group_id', 'competition_id',
                'discipline_id', 'display_type', 'id', 'max', 'min', 'order',
                'short', 'title', 'type',
            ])
            ->toArray();

        return new JsonResponse($categories);
    }

    /**
     * Store new record of resource instance.
     *
     * @param \App\Http\Requests\Category\Store $request
     * @param int                               $competitionId
     * @param int                               $disciplineId
     * @param int                               $groupId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function store(
        Store $request,
        int $competitionId,
        int $disciplineId,
        int $groupId
    ): JsonResponse
    {
        $details = $request->only([
            'area_id', 'category_group_id', 'competition_id', 'discipline_id',
            'display_type', 'max', 'min', 'short', 'title',
        ]);

        /** @var \App\Discipline $discipline */
        $discipline = $request->find('discipline');

        /** @var \App\CategoryGroup $group */
        $group = $request->find('group');

        $categoryCount = $this->categories->whereGroup($groupId)->count();

        $details['order'] = $categoryCount + 1;
        $details['type'] = $discipline->category_type;
        $details['discipline_title'] = $discipline->title;
        $details['discipline_short'] = $discipline->short;
        $details['category_group_title'] = $group->title;
        $details['category_group_short'] = $group->short;
        $details['team_id'] = $group->team_id;

        $category = $this->categories->create($details);

        return new JsonResponse($category);
    }

    /**
     * Get single resource instance.
     *
     * @param int $competitionId
     * @param int $disciplineId
     * @param int $groupId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id
    ): JsonResponse
    {
        $category = $this->categories->find($id);

        return new JsonResponse($category);
    }

    /**
     * Update existing resource instance.
     *
     * @param \App\Http\Requests\Category\Update $request
     * @param int                                $competitionId
     * @param int                                $disciplineId
     * @param int                                $groupId
     * @param int                                $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(
        Update $request,
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id
    ): JsonResponse
    {
        $category = $request->find('category');

        $details = $request->only([
            'area_id', 'display_type', 'max', 'min', 'short', 'title',
        ]);

        $this->categories->update($details, $id, $category);

        return new JsonResponse($category);
    }

    /**
     * Delete existing resource instance.
     *
     * @param \App\Http\Requests\Category\Destroy $request
     * @param int                                 $competitionId
     * @param int                                 $disciplineId
     * @param int                                 $groupId
     * @param int                                 $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function destroy(
        Destroy $request,
        int $competitionId,
        int $disciplineId,
        int $groupId,
        int $id
    ): JsonResponse
    {
        $category = $request->find('category');

        try {
            $category->delete();
        } catch (\Exception $e) {
            report($e);
            return new JsonResponse(false);
        }

        return new JsonResponse(true);
    }
}
