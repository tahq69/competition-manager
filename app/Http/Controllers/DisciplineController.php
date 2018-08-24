<?php namespace App\Http\Controllers;

use App\CategoryGroup;
use App\Contracts\ICategoryGroupRepository as IGroups;
use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Contracts\IDisciplineRepository as IDisciplines;
use App\Http\Requests\Discipline\Store;
use App\Http\Requests\Discipline\Update;
use DB;
use Illuminate\Http\JsonResponse;

/**
 * Class DisciplineController
 *
 * @package App\Http\Controllers
 */
class DisciplineController extends Controller
{
    /**
     * @var \App\Contracts\IDisciplineRepository
     */
    private $disciplines;

    /**
     * @var \App\Contracts\ICategoryGroupRepository
     */
    private $groups;

    /**
     * @var \App\Contracts\ICompetitionRepository
     */
    private $competitions;

    /**
     * CompetitionController constructor.
     *
     * @param \App\Contracts\IDisciplineRepository    $disciplines
     * @param \App\Contracts\ICategoryGroupRepository $groups
     * @param \App\Contracts\ICompetitionRepository   $competitions
     */
    public function __construct(
        IDisciplines $disciplines,
        IGroups $groups,
        ICompetitions $competitions)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->disciplines = $disciplines;
        $this->groups = $groups;
        $this->competitions = $competitions;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $competitionId
     *
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param int $competitionId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param int   $competitionId
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request, int $competitionId): JsonResponse
    {
        $details = $request->only([
            'title', 'short', 'type', 'game_type', 'description',
            'competition_id', 'category_group_type', 'category_type',
        ]);

        /** @var \App\Competition $competition */
        $competition = $this->competitions->find($competitionId, ['id', 'team_id']);
        $details['team_id'] = $competition->team_id;

        $discipline = $this->disciplines->create($details);

        return new JsonResponse($discipline);
    }

    /**
     * Update existing instance of competition discipline.
     *
     * @param \App\Http\Requests\Discipline\Update $request
     * @param  int                                 $competitionId
     * @param  int                                 $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     * @throws \Throwable
     */
    public function update(
        Update $request,
        int $competitionId,
        int $id
    ): JsonResponse
    {
        $discipline = $request->find('discipline');

        // Types is not allowed to update as they affect all data under already
        // existing discipline.
        $details = $request->only([
            'title', 'short', 'type', 'game_type', 'description',
        ]);

        DB::transaction(function () use ($details, $id, &$discipline) {
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
