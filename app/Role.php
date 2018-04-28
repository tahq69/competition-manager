<?php namespace App;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App
 */
class Role extends Model
{
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
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Belongs to many users relation.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class, 'role_user', 'role_id', 'user_id'
        )->withTimestamps();
    }
}