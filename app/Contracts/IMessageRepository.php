<?php namespace App\Contracts;

use Illuminate\Http\Request;

/**
 * Interface IMessageRepository
 *
 * @package App\Contracts
 */
interface IMessageRepository extends IPaginateRepository
{
    /**
     * Filter only incoming messages.
     *
     * @param int $userId
     *
     * @return $this
     */
    function filterInbox(int $userId);


    /**
     * Filter only outgoing messages.
     *
     * @param int $userId
     *
     * @return $this
     */
    function filterOutbox(int $userId);

    /**
     * Join replays to the current queryable.
     *
     * @param int $replyCount
     *
     * @return $this
     */
    function withReplays($replyCount = 10);

    /**
     * Count unread message count in inbox.
     *
     * @param int $userId
     *
     * @return int
     */
    function countUnread(int $userId);
}