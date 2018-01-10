<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Team
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string $short
 * @property string $logo
 * @property int $_credits
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TeamMember[] $managers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TeamMember[] $members
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasAuditTrait;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'teams';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'created_by',      // int(10) UNSIGNED
        'created_by_name', // varchar(255)
        'logo',            // varchar(1000)
        'name',            // varchar(255)
        'short',           // varchar(15)
        'updated_by',      // int(10) UNSIGNED
        'updated_by_name', // varchar(255)
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        '_credits'
    ];

    /**
     * Members relation.
     * @return HasMany
     */
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id')
            ->where('membership_type', TeamMember::MEMBER);
    }

    /**
     * Members relation.
     * @return HasMany
     */
    public function managers()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id')
            ->where('membership_type', TeamMember::MANAGER);
    }

    /**
     * @return HasMany
     */
    public function competitions()
    {
        return $this->hasMany(Competition::class, 'competition_id', 'id');
    }
}
