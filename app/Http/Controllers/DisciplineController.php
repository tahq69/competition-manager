<?php namespace App\Http\Controllers;

use App\CategoryGroup;
use App\Contracts\ICategoryGroupRepository as IGroups;
use App\Contracts\IDisciplineRepository as IDisciplines;
use App\Http\Requests\Discipline\Update as UpdateDisciplineRequest;
use App\Http\Requests\Discipline\Store as StoreDisciplineRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class DisciplineController
 * @package App\Http\Controllers
 */
class DisciplineController extends Controller
{
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
     * @param IDisciplines $disciplines
     * @param IGroups $groups
     */
    public function __construct(IDisciplines $disciplines, IGroups $groups)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->disciplines = $disciplines;
        $this->groups = $groups;
    }

    /**
     * Display a listing of the resource.
     * @param  int $competitionId
     * @return JsonResponse
     */
    public function index(int $competitionId): JsonResponse
    {
        $disciplines = $this->disciplines
            ->whereCompetition($competitionId)
            ->orderBy('id', 'desc')
            ->get(['competition_id', 'title', 'short', 'type', 'id']);

        return new JsonResponse($disciplines->toArray());
    }

    /**
     * Get single competition instance.
     * @param  int $competitionId
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $competitionId, int $id): JsonResponse
    {
        $discipline = $this->disciplines
            ->whereCompetition($competitionId)
            ->find($id);

        return new JsonResponse($discipline);
    }

    /**
     * Store new instance of competition discipline.
     * @param  int $competitionId
     * @param  StoreDisciplineRequest $request
     * @return JsonResponse
     */
    public function store(
        int $competitionId,
        StoreDisciplineRequest $request): JsonResponse
    {
        $details = $request->only([
            'title', 'short', 'type', 'game_type', 'description',
            'competition_id', 'category_group_type', 'category_type',
        ]);

        $discipline = $this->disciplines->create($details);

        return new JsonResponse($discipline);
    }

    /**
     * Update existing instance of competition discipline.
     * @param  int $competitionId
     * @param  int $id
     * @param  UpdateDisciplineRequest $request
     * @return JsonResponse
     * @throws \Exception|\Throwable
     */
    public function update(
        int $competitionId,
        int $id,
        UpdateDisciplineRequest $request): JsonResponse
    {
        $discipline = $this->disciplines
            ->whereCompetition($competitionId)
            ->find($id);

        // Types is not allowed to update as they affect all data under already
        // existing discipline.
        $details = $request->only([
            'title', 'short', 'type', 'game_type', 'description',
        ]);

        \DB::transaction(function() use ($details, $id, $discipline) {
            $this->disciplines->update($details, $id, $discipline);

            // Update category group names to be same as updated discipline.
            $groups = $this->groups->whereDiscipline($id)->get();
            $groups->each(function (CategoryGroup $group) use ($details) {
                $group->discipline_short = $details['short'];
                $group->discipline_title = $details['title'];

                $this->groups->update($group->toArray(), $group->id, $group);
            });
        });

        return new JsonResponse($discipline);
    }
}
