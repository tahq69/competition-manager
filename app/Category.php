<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Category
 * @package App
 */
class Category extends Model
{
    use HasAuditTrait, HasCompetitionTrait;

    const DISPLAY_MIN = 'MIN';
    const DISPLAY_MAX = 'MAX';
    const DISPLAY_BOTH = 'BOTH';

    const DISPLAY_TYPES = [
        self::DISPLAY_MIN, self::DISPLAY_MAX, self::DISPLAY_BOTH
    ];

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
        'display_type',         // varchar(255)
        'max',                  // int(10) UNSIGNED
        'min',                  // int(10) UNSIGNED
        'order',                // int(10) UNSIGNED
        'short',                // varchar(15)
        'title',                // varchar(255)
        'type',                 // varchar(255)
        'updated_by',           // int(10) UNSIGNED
        'updated_by_name',      // varchar(255)
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
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
