<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Category
 *
 * @package App
 * @property int $id
 * @property string $title
 * @property string $short
 * @property int $order
 * @property int $competition_id
 * @property int|null $area_id
 * @property string $discipline_title
 * @property string $discipline_short
 * @property int $discipline_id
 * @property string $category_group_title
 * @property string $category_group_short
 * @property int $category_group_id
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Area|null $area
 * @property-read \App\CategoryGroup $category_group
 * @property-read \App\Competition $competition
 * @property-read \App\User $creator
 * @property-read \App\Discipline $discipline
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasAuditTrait, HasCompetitionTrait;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'area_id',              // int(10) UNSIGNED
        'category_group_id',    // int(10) UNSIGNED
        'category_group_short', // varchar(255)
        'category_group_title', // varchar(255)
        'competition_id',       // int(10) UNSIGNED
        'created_by',           // int(10) UNSIGNED
        'created_by_name',      // varchar(255)
        'discipline_id',        // int(10) UNSIGNED
        'discipline_short',     // varchar(255)
        'discipline_title',     // varchar(255)
        'order',                // int(10) UNSIGNED
        'short',                // varchar(15)
        'title',                // varchar(255)
        'updated_by',           // int(10) UNSIGNED
        'updated_by_name',      // varchar(255)
    ];

    /**
     * @return BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function category_group()
    {
        return $this->belongsTo(CategoryGroup::class, 'category_group_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id', 'id');
    }
}
