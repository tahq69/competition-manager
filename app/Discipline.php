<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Discipline
 * @package App
 */
class Discipline extends Model
{
    use HasCompetitionTrait, HasAuditTrait;

    const TYPE_KICKBOXING = "KICKBOXING";

    const CAT_TYPE_WEIGHT = "WEIGHT";
    const CAT_TYPE_AGE = "AGE";

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'disciplines';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'category_group_type', // varchar(255)
        'category_type',       // varchar(255)
        'competition_id',      // int(10) UNSIGNED
        'created_by',          // int(10) UNSIGNED
        'created_by_name',     // varchar(255)
        'description',         // text
        'game_type',           // text
        'short',               // varchar(15)
        'title',               // varchar(255)
        'type',                // varchar(255)
        'updated_by',          // int(10) UNSIGNED
        'updated_by_name',     // varchar(255)
        'team_id',             // int(10) UNSIGNED
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * @return HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'discipline_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function category_groups()
    {
        return $this->hasMany(CategoryGroup::class, 'category_group_id', 'id');
    }
}
