<?php namespace App\Http\Controllers;

use App\Contracts\IMessageRepository;
use App\Contracts\ITeamMemberRepository;
use App\Message;
use App\TeamMember;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class InvitationController
 *
 * @package App\Http\Controllers
 */
class InvitationController extends Controller
{
    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * @var \App\Contracts\IMessageRepository
     */
    private $messages;

    /**
     * InvitationController constructor.
     *
     * @param \App\Contracts\ITeamMemberRepository $members
     * @param \App\Contracts\IMessageRepository    $messages
     */
    public function __construct(
        ITeamMemberRepository $members,
        IMessageRepository $messages)
    {
        $this->middleware('auth:api');
        $this->members = $members;
        $this->messages = $messages;
    }

    /**
     * Confirm team member invitation.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $messageId
     * @param int                      $teamMemberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmMember(Request $request, int $messageId, int $teamMemberId): JsonResponse
    {
        /** @var TeamMember $member */
        $member = $this->members->find($teamMemberId);

        if ($member->user_id != $request->user()->id) {
            $msg = __('Sorry, but this action is not for you!');
            return new JsonResponse($msg, 422);
        }

        if ($member->membership_type != TeamMember::INVITED) {
            $msg = __('Invitation is invalid or already accepted/refused');
            return new JsonResponse($msg, 422);
        }

        $model = ['membership_type' => TeamMember::MEMBER];
        $this->members->update($model, $member->id, $member);

        return new JsonResponse(__('Confirmation successfully completed'));
    }

    /**
     * Refuse team member invitation.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $messageId
     * @param int                      $teamMemberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refuseMember(Request $request, int $messageId, int $teamMemberId): JsonResponse
    {
        /** @var TeamMember $member */
        $member = $this->members->find($teamMemberId);

        /** @var Message $message */
        $message = $this->messages->find($messageId);

        if ($member->user_id != $request->user()->id) {
            $msg = __('Sorry, but this action is not for you!');
            return new JsonResponse($msg, 422);
        }

        if ($member->membership_type != TeamMember::INVITED) {
            $msg = __('Invitation is invalid or already accepted/refused');
            return new JsonResponse($msg, 422);
        }

        $model = ['membership_type' => TeamMember::MEMBER, 'user_id' => null];
        $this->members->update($model, $member->id, $member);
        $this->createRefuseMessage($request->user(), $message);

        return new JsonResponse(__('Refusal successfully completed'));
    }

    private function createRefuseMessage(User $user, Message $message)
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

        return $this->messages->create($response);
    }
}
