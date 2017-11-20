<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\ITeamMemberRepository;
use App\Http\Requests\UserRolesPolicy;
use App\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 * @package App\Http\Requests\TeamMembers
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param ITeamMemberRepository $members
     * @return bool
     */
    public function authorize(ITeamMemberRepository $members)
    {
        return Policy::canUpdate($members, $this->teamId());
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
                'max:255',
            ],
            'user_id' => [
                Rule::exists('users', 'id'),
                Rule::unique('team_members', 'user_id')
                    ->where('team_id', $this->teamId())
                    ->where('membership_type', TeamMember::MEMBER)
                    ->ignore($this->memberId()),
            ]
        ];
    }

    private function teamId()
    {
        return $this->parameters()['team'];
    }

    private function memberId()
    {
        return $this->parameters()['member'];
    }

    private function parameters()
    {
        return \Route::current()->parameters();
    }
}
