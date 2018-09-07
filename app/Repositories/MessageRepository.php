<?php namespace App\Repositories;

use App\Contracts\IMessageRepository;
use App\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class MessageRepository
 *
 * @package App\Repositories
 */
class MessageRepository
    extends PaginationRepository
    implements IMessageRepository
{
    /**
     * Get current repository full model class name
     *
     * @return string
     */
    function modelClass(): string
    {
        return Message::class;
    }


    /**
     * Filter only incoming messages.
     *
     * @param int $userId
     *
     * @return $this
     */
    function filterInbox(int $userId)
    {
        return $this->setWhere('to_id', $userId);
    }

    /**
     * Filter only outgoing messages.
     *
     * @param int $userId
     *
     * @return $this
     */
    function filterOutbox(int $userId)
    {
        return $this->setWhere('from_id', $userId);
    }

    /**
     * Join replays to the current queryable.
     *
     * @param int $replyCount
     *
     * @return $this
     */
    function withReplays($replyCount = 10)
    {
        $with = join('.', array_fill(0, $replyCount, 'replyOn'));

        return $this->setQuery(function (Builder $query) use ($with) {
            return $query->with($with);
        });
    }

    /**
     * Count unread message count in inbox.
     *
     * @param int $userId
     *
     * @return int
     */
    function countUnread(int $userId)
    {
        return $this->filterInbox($userId)
            ->getQuery()
            ->where('is_read', false)
            ->count('id');
    }
}