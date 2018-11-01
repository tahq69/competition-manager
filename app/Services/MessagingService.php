<?php namespace App\Services;

use App\Contracts\IMessageRepository;
use App\Contracts\IUserRepository;
use App\Message;
use App\Team;
use App\TeamMember;
use App\User;

/**
 * Class MessagingService
 *
 * @package App\Services
 */
class MessagingService
{
    /**
     * @var \App\Contracts\IMessageRepository
     */
    private $messages;

    /**
     * @var \App\Contracts\IUserRepository
     */
    private $users;

    /**
     * MessagingService constructor.
     *
     * @param \App\Contracts\IMessageRepository $messages
     * @param \App\Contracts\IUserRepository    $users
     */
    public function __construct(IMessageRepository $messages, IUserRepository $users)
    {
        $this->messages = $messages;
        $this->users = $users;
    }

    /**
     * Send invitation message to approve team membership.
     *
     * @param int $fromUser User identifier of sender.
     * @param int $toUser   User identifier of recipient.
     *
     *
     * @return \App\Message
     */
    public function sendTeamMemberInvitation(
        int $fromUser, int $toUser, Team $team, TeamMember $member
    ): \App\Message
    {
        $type = Message::TEAM_MEMBER_INVITATION;

        /** @var \App\User $sender */
        $sender = $this->users->find($fromUser, ['id', 'name']);

        /** @var \App\User $recipient */
        $recipient = $this->users->find($toUser, ['id', 'name']);

        $subject = __(':user has invited you to join :team team', [
            'user' => $sender->name,
            'team' => $team->name,
        ]);

        $message = [
            'subject' => $subject,
            'body' => $type,
            'importance_level' => 7,
            'to_id' => $recipient->id,
            'to_name' => $recipient->name,
            'from_id' => $sender->id,
            'from_name' => $sender->name,
            'type' => $type,
            'payload' => [
                'from_team_name' => $team->name,
                'from_user_name' => $sender->name,
                'member_id' => $member->id,
                'completed' => false,
            ],
        ];

        /** @var \App\Message $createdMessage */
        $createdMessage = $this->messages->create($message);

        return $createdMessage;
    }

    /**
     * Completes message payload.
     *
     * @param \App\Message $message
     */
    public function completeMessage(\App\Message $message)
    {
        $payload = $message->payload;
        $payload['completed'] = true;

        $this->messages->update(compact('payload'), $message->id, $message);
    }

    /**
     * @param \App\User    $user
     * @param \App\Message $message
     *
     * @return \App\Message
     */
    public function refuseTeamMemberInvitation(User $user, Message $message)
    {
        $response = [
            'subject' => 'Re: ' . $message->subject,
            'body' => __('User refused your invitation to become team member.'),
            'from_id' => $user->id,
            'from_name' => $user->name,
            'to_name' => $message->from_name,
            'to_id' => $message->from_id,
            'reply' => $message->id,
            'reply_count' => $message->reply_count + 1,
            'payload' => [],
            'type' => Message::USER_MESSAGE,
            'importance_level' => 7,
        ];

        /** @var \App\Message $newMessage */
        $newMessage = $this->messages->create($response);
        $this->completeMessage($message);

        return $newMessage;
    }
}