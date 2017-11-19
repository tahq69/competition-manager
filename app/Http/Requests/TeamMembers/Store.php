<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\ITeamMemberRepository;
use App\Http\Requests\UserRolesValidationRequest;
use App\Role;
use App\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 * @package App\Http\Requests\TeamMembers
 */
class Store extends FormRequest
{
    use UserRolesValidationRequest;

    /**
     * Determine if the user is authorized to make this request.
     * @param ITeamMemberRepository $members
     * @return bool
     */
    public function authorize(ITeamMemberRepository $members)
    {
        if (!\Auth::check()) {
            return false;
        }

        $user = \Auth::user();
        $roles = $this->roles($user);

        // Super Admin can create anything and for anyone.
        if ($this->hasRole($roles, Role::SUPER_ADMIN)) return true;

        $isManager = $members
            ->filterByTeam($this->teamId())
            ->filterByUser($user->id)
            ->filterByMembership(TeamMember::MANAGER)
            ->count();

        // If current user is team manager - he can add new members.
        if ($isManager > 0) return true;

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255'
            ],
            'user_id' => [
                Rule::exists('users', 'id'),
                Rule::unique('team_members', 'user_id')
                    ->where('team_id', $this->teamId())
                    ->where('membership_type', TeamMember::MEMBER)
            ]
        ];
    }

    private function teamId()
    {
        return \Route::current()->parameters()['team'];
    }

    public function ajax()
    {
        return true;
    }
}
