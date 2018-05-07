<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Area
 * @package App
 */
class Area extends Model
{
    use HasAuditTrait, HasCompetitionTrait;

    const ARENA = 'ARENA';
    const CAGE = 'CAGE';
    const RING = 'RING';
    const TATAMI = 'TATAMI';

    const TYPES = [
        self::ARENA, self::CAGE, self::RING, self::TATAMI
    ];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'areas';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'competition_id',  // int(10) UNSIGNED
        'created_by',      // int(10) UNSIGNED
        'created_by_name', // varchar(255)
        'description',     // text NULL
        'nr',              // int(10) NULL
        'title',           // varchar(255)
        'type',            // varchar(255)
        'updated_by',      // int(10) UNSIGNED
        'updated_by_name', // varchar(255)
        'team_id',         // int(10) UNSIGNED
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
        return $this->hasMany(Category::class, 'area_id', 'id');
    }
}
