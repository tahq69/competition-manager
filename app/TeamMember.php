<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TeamMember
 *
 * @package App
 * @property int $id
 * @property int|null $user_id
 * @property int $team_id
 * @property string $name
 * @property string $membership_type
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $creator
 * @property-read \App\User|null $modifier
 * @property-read \App\Team $team
 * @property-read \App\User|null $user
 * @mixin \Eloquent
 */
class TeamMember extends Model
{
    use HasAuditTrait;

    const INVITED = 'invited';
    const MEMBER = 'member';
    const MANAGER = 'manager';

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'team_members';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'created_by',      // int(10) UNSIGNED
        'created_by_name', // varchar(255)
        'membership_type', // varchar(255)
        'name',            // varchar(255)
        'team_id',         // int(10) UNSIGNED
        'updated_by',      // int(10) UNSIGNED NULL
        'updated_by_name', // varchar(255) ''
        'user_id',         // int(10) UNSIGNED NULL
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
