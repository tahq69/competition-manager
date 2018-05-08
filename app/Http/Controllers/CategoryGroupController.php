<?php namespace App\Http\Controllers;

use App\Contracts\ICategoryGroupRepository as IGroups;
use App\Contracts\IDisciplineRepository as IDisciplines;
use App\Http\Requests\CategoryGroup\Destroy as DestroyGroupRequest;
use App\Http\Requests\CategoryGroup\Store as StoreGroupRequest;
use App\Http\Requests\CategoryGroup\Update as UpdateGroupRequest;
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
     * @var \App\Contracts\IDisciplineRepository
     */
    private $disciplines;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\ICategoryGroupRepository $groups
     * @param \App\Contracts\IDisciplineRepository    $disciplines
     */
    public function __construct(IGroups $groups, IDisciplines $disciplines)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->groups = $groups;
        $this->disciplines = $disciplines;
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
     * Store new instance of resource instance.
     *
     * @param int                                    $competitionId
     * @param int                                    $disciplineId
     * @param \App\Http\Requests\CategoryGroup\Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        int $competitionId,
        int $disciplineId,
        StoreGroupRequest $request
    ): JsonResponse
    {
        $details = $request->only([
            'title', 'short', 'rounds', 'time', 'min', 'max', 'competition_id',
            'discipline_id',
        ]);

        /** @var \App\Discipline $discipline */
        $discipline = $this->disciplines->find($disciplineId);
        $groupCount = $this->groups->whereDiscipline($disciplineId)->count();

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
     * @param int                                     $competitionId
     * @param int                                     $disciplineId
     * @param int                                     $id
     * @param \App\Http\Requests\CategoryGroup\Update $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        int $competitionId,
        int $disciplineId,
        int $id,
        UpdateGroupRequest $request
    ): JsonResponse
    {
        $group = $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->find($id);

        $details = $request->only([
            'title', 'short', 'rounds', 'time', 'min', 'max',
        ]);

        $this->groups->update($details, $id, $group);

        return new JsonResponse($group);
    }

    /**
     * @param  int                                     $competitionId
     * @param  int                                     $disciplineId
     * @param  int                                     $id
     * @param \App\Http\Requests\CategoryGroup\Destroy $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(
        int $competitionId,
        int $disciplineId,
        int $id,
        DestroyGroupRequest $request
    ): JsonResponse
    {
        $this->groups
            ->whereCompetition($competitionId)
            ->whereDiscipline($disciplineId)
            ->find($id)
            ->delete();

        return new JsonResponse(true);
    }
}
