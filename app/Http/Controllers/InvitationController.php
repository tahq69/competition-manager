<?php namespace App\Http\Controllers;

use App\Contracts\IMessageRepository;
use App\Contracts\ITeamMemberRepository;
use App\Message;
use App\Services\MessagingService;
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
     * @var \App\Services\MessagingService
     */
    private $messagingService;

    /**
     * InvitationController constructor.
     *
     * @param \App\Contracts\ITeamMemberRepository $members
     * @param \App\Contracts\IMessageRepository    $messages
     * @param \App\Services\MessagingService       $messagingService
     */
    public function __construct(
        ITeamMemberRepository $members,
        IMessageRepository $messages,
        MessagingService $messagingService)
    {
        $this->middleware('auth:api');
        $this->members = $members;
        $this->messages = $messages;
        $this->messagingService = $messagingService;
    }

    /**
     * Confirm team member invitation.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $messageId
     * @param int                      $teamMemberId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function confirmMember(Request $request, int $messageId, int $teamMemberId): JsonResponse
    {
        $authUserId = $request->user()->id;

        /** @var TeamMember $member */
        $member = $this->members->find($teamMemberId);

        /** @var Message $message */
        $message = $this->messages->find($messageId);

        if ($authUserId != $message->to_id) {
            $msg = __('Sorry, but this action is not for you!');
            return new JsonResponse($msg, 422);
        }

        if ($member->membership_type != TeamMember::INVITED) {
            $msg = __('Invitation is invalid or already accepted/refused');
            return new JsonResponse($msg, 422);
        }

        try {
            \DB::beginTransaction();

            $model = ['membership_type' => TeamMember::MEMBER, 'user_id' => $authUserId];
            $this->members->update($model, $member->id, $member);
            $this->messagingService->completeMessage($message);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw new \Exception(
                'Internal database transaction error occurred.', 507, $exception
            );
        }


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
     * @throws \Exception
     */
    public function refuseMember(Request $request, int $messageId, int $teamMemberId): JsonResponse
    {
        /** @var TeamMember $member */
        $member = $this->members->find($teamMemberId);

        /** @var Message $message */
        $message = $this->messages->find($messageId);

        if ($request->user()->id != $message->to_id) {
            $msg = __('Sorry, but this action is not for you!');
            return new JsonResponse($msg, 422);
        }

        if ($member->membership_type != TeamMember::INVITED) {
            $msg = __('Invitation is invalid or already accepted/refused');
            return new JsonResponse($msg, 422);
        }


        try {
            \DB::beginTransaction();

            $model = ['membership_type' => TeamMember::MEMBER, 'user_id' => null];
            $this->members->update($model, $member->id, $member);
            $this->messagingService->refuseTeamMemberInvitation($request->user(), $message);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw new \Exception(
                'Internal database transaction error occurred.', 507, $exception
            );
        }

        return new JsonResponse(__('Refusal successfully completed'));
    }
}
