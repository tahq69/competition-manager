<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Team
 * @package App
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
