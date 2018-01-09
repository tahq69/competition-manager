<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property int $id
 * @property string $key
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @mixin \Eloquent
 */
class Role extends Model
{
    // ------------------- user specific roles   -----------------------------//
    // Can do any action in system
    const SUPER_ADMIN = 'SUPER_ADMIN'; // User

    // Can create/manage own posts
    const CREATE_POST = 'CREATE_POST'; // User
    const MANAGE_POSTS = 'MANAGE_POSTS'; // User

    // Can manage users
    const MANAGE_USERS = 'MANAGE_USERS'; // User
    const MANAGE_USER_ROLES = 'MANAGE_USER_ROLES'; // User

    // Can create/manage teams
    const CREATE_TEAMS = 'CREATE_TEAMS'; // User

    // ------------------- member specific roles -----------------------------//

    const MANAGE_TEAMS = 'MANAGE_TEAMS'; // TeamMember
    const MANAGE_MEMBERS = 'MANAGE_MEMBERS'; // TeamMember
    const MANAGE_MEMBER_ROLES = 'MANAGE_MEMBER_ROLES'; // TeamMember

    // Can manage assigned competitions
    const CREATE_COMPETITIONS = 'CREATE_COMPETITIONS'; // TeamMember
    const MANAGE_COMPETITIONS = 'MANAGE_COMPETITIONS'; // TeamMember
    const MANAGE_COMPETITION_AREAS = 'MANAGE_COMPETITION_AREAS'; // TeamMember
    const MANAGE_COMPETITION_DISCIPLINES = 'MANAGE_COMPETITION_DISCIPLINES'; // TeamMember
    const MANAGE_COMPETITION_MEMBERS = 'MANAGE_COMPETITION_MEMBERS'; // TeamMember
    const MANAGE_COMPETITION_JUDGES = 'MANAGE_COMPETITION_JUDGES'; // TeamMember

    const ALL_ROLES = [
        Role::SUPER_ADMIN,
        Role::CREATE_POST,
        Role::MANAGE_POSTS,
        Role::CREATE_TEAMS,
        Role::MANAGE_TEAMS,
        Role::MANAGE_MEMBERS,
        Role::MANAGE_MEMBER_ROLES,
        Role::MANAGE_USERS,
        Role::MANAGE_USER_ROLES,
        Role::MANAGE_COMPETITIONS,
        Role::CREATE_COMPETITIONS,
        Role::MANAGE_COMPETITION_AREAS,
        Role::MANAGE_COMPETITION_DISCIPLINES,
        Role::MANAGE_COMPETITION_JUDGES,
        Role::MANAGE_COMPETITION_MEMBERS,
    ];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'key',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class, 'role_user', 'role_id', 'user_id'
        )->withTimestamps();
    }
}