<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Competition
 *
 * @package App
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property string $cooperation
 * @property string $invitation
 * @property string $program
 * @property string $rules
 * @property string $ambulance
 * @property string $prizes
 * @property string $equipment
 * @property string $price
 * @property string|null $organization_date
 * @property string|null $registration_till
 * @property int|null $judge_id
 * @property string $judge_name
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $creator
 * @property-read \App\User|null $judge
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $managers
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class Competition extends Model
{
    use HasAuditTrait;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'competitions';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'ambulance',         // longtext
        'cooperation',       // longtext
        'created_by',        // int(10) UNSIGNED
        'created_by_name',   // varchar(255)
        'equipment',         // longtext
        'invitation',        // longtext
        'judge_id',          // int(10) UNSIGNED NULL
        'judge_name',        // varchar(255)
        'organization_date', // timestamp NULL
        'price',             // longtext
        'prizes',            // longtext
        'program',           // longtext
        'registration_till', // timestamp NULL
        'rules',             // longtext
        'subtitle',          // varchar(255)
        'title',             // varchar(255)
        'updated_by',        // int(10) UNSIGNED
        'updated_by_name',   // varchar(255)
    ];

    /**
     * @return BelongsTo
     */
    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function managers()
    {
        return $this->belongsToMany(
            User::class, 'competition_managers', 'competition_id', 'user_id'
        )->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function disciplines()
    {
        return $this->hasMany(Discipline::class, 'competition_id', 'id');
    }
}
