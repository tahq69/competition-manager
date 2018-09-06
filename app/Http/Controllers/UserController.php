<?php namespace App\Http\Controllers;

use App\Contracts\IUserRepository;
use App\Http\Requests\User\StoreUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var IUserRepository
     */
    private $users;

    /**
     * UserController constructor.
     *
     * @param IUserRepository $users
     */
    public function __construct(IUserRepository $users)
    {
        $this->middleware('auth:api')
            ->except('search', 'store', 'show');

        $this->middleware('guest')
            ->only('store');

        $this->users = $users;
    }

    /**
     * Display authenticated user resource data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $user = $this->users
            ->withRoles()
            ->findWithTeamRoles($userId);

        return new JsonResponse($user);
    }

    /**
     * Search user entries by name property.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $users = $this->users
            ->searchByName($request->term ?: '')
            ->paginate($request->perPage ?: 15, [], ['id', 'name']);

        return new JsonResponse($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUser $request
     *
     * @return JsonResponse
     */
    public function store(StoreUser $request): JsonResponse
    {
        $user = $this->users->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return new JsonResponse($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = $this->users->withTeams()->find($id);

        return new JsonResponse($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        //
    }
}
