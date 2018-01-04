<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CategoryGroup
 *
 * @package App
 * @property int $id
 * @property string $title
 * @property string $short
 * @property int $competition_id
 * @property string $discipline_title
 * @property string $discipline_short
 * @property int $discipline_id
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 * @property-read \App\Competition $competition
 * @property-read \App\User $creator
 * @property-read \App\Discipline $discipline
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class CategoryGroup extends Model
{
    use HasCompetitionTrait, HasAuditTrait;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'category_groups';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'competition_id',   // int(10) UNSIGNED
        'created_by',       // int(10) UNSIGNED
        'created_by_name',  // varchar(255)
        'discipline_id',    // int(10) UNSIGNED
        'discipline_short', // varchar(255)
        'discipline_title', // varchar(255)
        'short',            // varchar(15)
        'title',            // varchar(255)
        'updated_by',       // int(10) UNSIGNED
        'updated_by_name',  // varchar(255)
    ];

    /**
     * @return BelongsTo
     */
    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }
}
