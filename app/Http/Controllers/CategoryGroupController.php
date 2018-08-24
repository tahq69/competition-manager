<?php namespace App\Http\Controllers;

use App\Contracts\ICategoryGroupRepository;
use App\Http\Requests\CategoryGroup\Destroy;
use App\Http\Requests\CategoryGroup\Store;
use App\Http\Requests\CategoryGroup\Update;
use Illuminate\Http\JsonResponse;

/**
 * Class CategoryGroupController
 *
 * @package App\Http\Controllers
 */
class CategoryGroupController extends Controller
{
    /**
     * @var \App\Contracts\ICategoryGroupRepository
     */
    private $groups;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICategoryGroupRepository $groups
     */
    public function __construct(ICategoryGroupRepository $groups)
    {
        $this->middleware('auth:api')
            ->except('index', 'show', 'categories');

        $this->groups = $groups;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $competitionId
     * @param int $disciplineId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $competitionId, int $disciplineId): JsonResponse
    {
        $groups = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->sortByOrder()
            ->get([
                'competition_id', 'discipline_id', 'id', 'max', 'min', 'order',
                'rounds', 'short', 'time', 'title', 'type',
            ]);

        return new JsonResponse($groups->toArray());
    }

    /**
     * Display a listing of the resource with categories.
     *
     * @param int $competitionId
     * @param int $disciplineId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(int $competitionId, int $disciplineId): JsonResponse
    {
        $groups = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->withCategories()
            ->sortByOrder()
            ->get([
                'competition_id', 'discipline_id', 'id', 'max', 'min', 'order',
                'rounds', 'short', 'time', 'title', 'type',
            ]);

        return new JsonResponse($groups);
    }

    /**
     * Store new instance of resource instance.
     *
     * @param \App\Http\Requests\CategoryGroup\Store $request
     * @param int                                    $competitionId
     * @param int                                    $disciplineId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function store(
        Store $request,
        int $competitionId,
        int $disciplineId
    ): JsonResponse
    {
        $details = $request->only([
            'title', 'short', 'rounds', 'time', 'min', 'max', 'competition_id',
            'discipline_id',
        ]);

        /** @var \App\Discipline $discipline */
        $discipline = $request->find('discipline');
        $groupCount = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->count();

        // Filling information from parent records.
        $details['order'] = $groupCount + 1;
        $details['type'] = $discipline->category_group_type;
        $details['discipline_title'] = $discipline->title;
        $details['discipline_short'] = $discipline->short;
        $details['team_id'] = $discipline->team_id;

        $group = $this->groups->create($details);

        return new JsonResponse($group);
    }

    /**
     * Get single resource instance.
     *
     * @param int $competitionId
     * @param int $disciplineId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(
        int $competitionId,
        int $disciplineId,
        int $id
    ): JsonResponse
    {
        $group = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->find($id);

        return new JsonResponse($group);
    }

    /**
     * Update existing resource instance.
     *
     * @param \App\Http\Requests\CategoryGroup\Update $request
     * @param int                                     $competitionId
     * @param int                                     $disciplineId
     * @param int                                     $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(
        Update $request,
        int $competitionId,
        int $disciplineId,
        int $id
    ): JsonResponse
    {
        $group = $request->find('group');

        $details = $request->only([
            'title', 'short', 'rounds', 'time', 'min', 'max',
        ]);

        $this->groups->update($details, $id, $group);

        return new JsonResponse($group);
    }

    /**
     * Delete existing resource instance.
     *
     * @param \App\Http\Requests\CategoryGroup\Destroy $request
     * @param int                                      $competitionId
     * @param int                                      $disciplineId
     * @param int                                      $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function destroy(
        Destroy $request,
        int $competitionId,
        int $disciplineId,
        int $id
    ): JsonResponse
    {
        $group = $request->find('group');

        try {
            $group->delete();
        } catch (\Exception $e) {
            report($e);
            return new JsonResponse(false);
        }

        return new JsonResponse(true);
    }
}
