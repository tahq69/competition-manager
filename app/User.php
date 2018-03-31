<?php namespace App;

use Crip\Core\Helpers\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, UserRoles;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'email', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'md5',
    ];

    /**
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class, 'role_user', 'user_id', 'role_id'
        )->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function memberships()
    {
        return $this->hasMany(TeamMember::class, 'user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(
            Team::class, 'team_members', 'user_id', 'team_id'
        )->wherePivot('membership_type', TeamMember::MEMBER);
    }

    /**
     * Encode user email in md5
     * @return string
     */
    public function getMd5Attribute(): string
    {
        if (array_key_exists('email', $this->attributes)) {
            return md5($this->attributes['email']);
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        if (!\Auth::check()) return false;

        $role = $this->roles()->where('key', Role::SUPER_ADMIN)->first(['roles.id']);

        return !!$role;
    }

    /**
     * @return string
     */
    public function slug(): string
    {
        return Str::slug($this->name . ' ' . $this->id, '_');
    }
}
