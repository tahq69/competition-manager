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
    // Can do any action in system
    const SUPER_ADMIN = 'SUPER_ADMIN';

    // Can create/manage own posts
    const CREATE_POST = 'CREATE_POST';
    // Can manage other user posts
    const MANAGE_POSTS = 'MANAGE_POSTS';

    // Can create/manage teams
    const CREATE_TEAMS = 'CREATE_TEAMS';

    // Can manage assigned competitions
    const EDIT_COMPETITIONS = 'EDIT_COMPETITIONS';
    // Can create/manage own team competitions
    const CREATE_COMPETITIONS = 'CREATE_COMPETITIONS';

    const ALL_ROLES = [
        Role::SUPER_ADMIN,
        Role::CREATE_POST,
        Role::MANAGE_POSTS,
        Role::CREATE_TEAMS,
        Role::EDIT_COMPETITIONS,
        Role::CREATE_COMPETITIONS,
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