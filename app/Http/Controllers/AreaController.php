<?php namespace App\Http\Controllers;

use App\Contracts\IAreaRepository as IAreas;
use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Http\Requests\Area\Destroy;
use App\Http\Requests\Area\Index;
use App\Http\Requests\Area\Store;
use App\Http\Requests\Area\Update;
use Illuminate\Http\JsonResponse;

/**
 * Class AreaController
 *
 * @package App\Http\Controllers\Auth
 */
class AreaController extends Controller
{
    /**
     * @var \App\Contracts\IAreaRepository
     */
    private $areas;

    /**
     * @var \App\Contracts\ICompetitionRepository
     */
    private $competitions;

    /**
     * AreaController constructor.
     *
     * @param \App\Contracts\IAreaRepository        $areas
     * @param \App\Contracts\ICompetitionRepository $competitions
     */
    public function __construct(IAreas $areas, ICompetitions $competitions)
    {
        $this->middleware('auth:api')
            ->except('index', 'show');

        $this->areas = $areas;
        $this->competitions = $competitions;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Area\Index $request
     * @param int                           $cmId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Index $request, int $cmId): JsonResponse
    {
        $orderingMapping = [
            'id' => 'id',
            'title' => 'title',
            'type' => 'type',
            'nr' => 'nr',
        ];

        $areas = $this->areas
            ->whereCompetition($cmId)
            ->setupOrdering($request, $orderingMapping)
            ->get();

        return new JsonResponse($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Area\Store $request
     * @param int                           $cmId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function store(Store $request, int $cmId): JsonResponse
    {
        $competition = $request->find('competition');

        $validatedInput = array_keys($request->rules());
        $details = $request->only($validatedInput);
        $details['team_id'] = $competition->team_id;

        $area = $this->areas->create($details);

        return new JsonResponse($area);
    }

    /**
     * Display the specified resource.
     *
     * @param int $cmId
     * @param int $areaId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $cmId, int $areaId): JsonResponse
    {
        $area = $this->areas->find($areaId);

        return new JsonResponse($area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Area\Update $request
     * @param int                            $cmId
     * @param int                            $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function update(Update $request, int $cmId, int $id): JsonResponse
    {
        $area = $request->find('area');

        $validatedInput = array_keys($request->rules());
        $details = $request->only($validatedInput);

        $this->areas->update($details, $id, $area);

        return new JsonResponse($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Area\Destroy $request
     * @param int                             $cmId
     * @param int                             $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function destroy(Destroy $request, int $cmId, int $id): JsonResponse
    {
        $area = $request->find('area');

        try {
            $area->delete();
        } catch (\Exception $e) {
            report($e);
            return new JsonResponse(false);
        }

        return new JsonResponse(true);
    }
}
