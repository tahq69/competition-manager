<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @package App
 * @property int $id
 * @property string $locale
 * @property string $title
 * @property string $body
 * @property string $image
 * @property string $state
 * @property \Carbon\Carbon|null $publish_at
 * @property int $author_id
 * @property int $created_by
 * @property string $created_by_name
 * @property int|null $updated_by
 * @property string $updated_by_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $author
 * @property-read \App\User $creator
 * @property-read string $date_from_now
 * @property-read string $short_body
 * @property-read \App\User|null $modifier
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasAuditTrait;

    const STATE_DRAFT = 'DRAFT';
    const STATE_PENDING = 'PENDING';
    const STATE_PRIVATE = 'PRIVATE';
    const STATE_PUBLISHED = 'PUBLISHED';
    const STATE_TRASH = 'TRASH';

    const STATES = [
        Post::STATE_DRAFT,
        Post::STATE_PENDING,
        Post::STATE_PRIVATE,
        Post::STATE_PUBLISHED,
        Post::STATE_TRASH,
    ];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'image', 'state', 'publish_at', 'author_id', 'locale',
        'created_by',           // int(10) UNSIGNED
        'created_by_name',      // varchar(255)
        'updated_by',           // int(10) UNSIGNED
        'updated_by_name',      // varchar(255)
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'publish_at'
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'date_from_now',
        'short_body'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    /**
     * @return string
     */
    public function getDateFromNowAttribute()
    {
        return $this->publish_at->diffForHumans();
    }

    /**
     * @return string
     */
    public function getShortBodyAttribute()
    {
        if (array_key_exists('body', $this->attributes))
            return str_limit(strip_tags($this->attributes['body']), 500);

        return '';
    }
}
