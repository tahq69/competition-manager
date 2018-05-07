<?php namespace App\Http\Controllers;

use App\Contracts\IAreaRepository as IAreas;
use Illuminate\Http\JsonResponse;

/**
 * Class AreaController
 *
 * @package App\Http\Controllers\Auth
 */
class AreaController extends Controller
{
    /**
     * @var IAreas
     */
    private $areas;

    /**
     * AreaController constructor.
     *
     * @param IAreas $areas
     */
    public function __construct(IAreas $areas)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->areas = $areas;
    }

    /**
     * Get single resource instance.
     *
     * @param int $competitionId
     *
     * @return JsonResponse
     */
    public function index(int $competitionId): JsonResponse
    {
        $areas = $this->areas
            ->whereCompetition($competitionId)
            ->orderBy('id', 'desc')
            ->get();

        return new JsonResponse($areas->toArray());
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $competitionId
     * @param int $areaId
     *
     * @return JsonResponse
     */
    public function show(int $competitionId, int $areaId): JsonResponse
    {
        $area = $this->areas->find($areaId);

        return new JsonResponse($area);
    }
}
