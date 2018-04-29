<?php namespace App;

use App\Exceptions\CompetitionCompletedException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Competition
 *
 * @package App
 */
class Competition extends Model
{
    use HasAuditTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'competitions';

    /**
     * The attributes that are mass assignable.
     *
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
        'team_id',           // int(10) UNSIGNED NULL
        'team_name',         // varchar(255)
        'team_short',        // varchar(15)
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'organization_date', 'registration_till',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disciplines()
    {
        return $this->hasMany(Discipline::class, 'competition_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * @throws \App\Exceptions\CompetitionCompletedException
     */
    public function ensureIsEditable()
    {
        if (!$this->registration_till->gt(Carbon::yesterday())) {
            throw new CompetitionCompletedException($this);
        }
    }
}
