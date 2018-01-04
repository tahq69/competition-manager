<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Discipline
 *
 * @package App
 * @property int $id
 * @property int $competition_id
 * @property string $title
 * @property string $short
 * @property string $type
 * @property string $game_type
 * @property string $description
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CategoryGroup[] $category_groups
 * @property-read \App\Competition $competition
 * @property-read \App\User $creator
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class Discipline extends Model
{
    use HasCompetitionTrait, HasAuditTrait;

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
        'competition_id',  // int(10) UNSIGNED
        'short',           // varchar(15)
        'title',           // varchar(255)
        'type',            // varchar(255)
        'game_type',       // text
        'description',     // text
        'created_by',      // int(10) UNSIGNED
        'created_by_name', // varchar(255)
        'updated_by',      // int(10) UNSIGNED
        'updated_by_name', // varchar(255)
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
