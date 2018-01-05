<?php namespace App\Http\Controllers;

use App\Contracts\IDisciplineRepository as Disciplines;
use App\Http\Requests\Discipline\Update as UpdateDisciplineRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class DisciplineController
 * @package App\Http\Controllers
 */
class DisciplineController extends Controller
{
    /**
     * @var Disciplines
     */
    private $disciplines;

    /**
     * CompetitionController constructor.
     * @param Disciplines $disciplines
     */
    public function __construct(Disciplines $disciplines)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->disciplines = $disciplines;
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
            ->get([], ['competition_id', 'title', 'short', 'type', 'id']);

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
     * Update existing instance of competition discipline.
     * @param  int $competitionId
     * @param  int $id
     * @param  UpdateDisciplineRequest $request
     * @return JsonResponse
     */
    public function update(
        int $competitionId, int $id,
        UpdateDisciplineRequest $request): JsonResponse
    {
        $discipline = $this->disciplines
            ->whereCompetition($competitionId)
            ->find($id);

        $details = $request->only([
            'title', 'short', 'type', 'game_type', 'description',
        ]);

        $this->disciplines->update($details, $id, $discipline);

        return new JsonResponse($discipline);
    }
}