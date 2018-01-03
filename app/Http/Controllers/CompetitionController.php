<?php namespace App\Http\Controllers;

use App\Contracts\ICompetitionRepository as Competitions;
use App\Contracts\IUserRepository as Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CompetitionController
 * @package App\Http\Controllers
 */
class CompetitionController extends Controller
{
    /**
     * @var Competitions
     */
    private $competitions;

    /**
     * @var Users
     */
    private $users;

    /**
     * CompetitionController constructor.
     * @param Competitions $competitions
     * @param Users $users
     */
    public function __construct(Competitions $competitions, Users $users)
    {
        $this->middleware('auth:api')
            ->except('index');

        $this->competitions = $competitions;
        $this->users = $users;
    }

    /**
     * Display a listing of the resource.
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->owned) {
            $this->competitions->filterOwnedOrManaged();
        }

        $competitions = $this->competitions->paginate($request->per_page ?: 15);

        return new JsonResponse($competitions);
    }
}
