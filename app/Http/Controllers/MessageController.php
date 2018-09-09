<?php namespace App\Http\Controllers;

use App\Contracts\IMessageRepository;
use App\Contracts\IUserRepository;
use App\Http\Requests\Message\Reply;
use App\Http\Requests\Message\Store;
use App\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
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
     * MessageController constructor.
     *
     * @param \App\Contracts\IMessageRepository $messages
     * @param \App\Contracts\IUserRepository    $users
     */
    public function __construct(IMessageRepository $messages, IUserRepository $users)
    {
        $this->middleware('auth:api');
        $this->messages = $messages;
        $this->users = $users;
    }

    /**
     * GET    /api/user/messages[?type=outbox]
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $perPage = $request->per_page ?: 15;

        $orderingMapping = [
            'id' => 'id',
            'subject' => 'subject',
            'from' => 'from_name',
        ];

        $this->messages->setupOrdering(
            $request, $orderingMapping, 'created_at', 'desc'
        );

        if ($request->type == 'outbox') {
            $this->messages->filterOutbox($userId);
        } else {
            $this->messages->filterInbox($userId);
        }

        $messages = $this->messages->paginate($perPage, [], [
            'id', 'subject', 'body', 'to_id', 'to_name', 'is_read',
            'importance_level', 'type', 'from_id', 'from_name', 'created_at',
            'reply', 'reply_count',
        ]);

        return new JsonResponse($messages);
    }

    /**
     * GET    /api/user/messages/read/{message}
     *
     * @param \Illuminate\Http\Request $request
     * @param  int                     $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $message = $this->messages->find($id);

        // mark message as read if recipient requests its details
        if (!$message->is_read && $message->to_id == $userId) {
            $this->messages->update(['is_read' => true], $id, $message);
        }

        $messages = $this->messages
            ->withReplays(10)
            ->find($message->id);

        return new JsonResponse($messages);
    }

    /**
     * GET    /api/user/messages/count/unread
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countUnread(Request $request)
    {
        $userId = $request->user()->id;
        $count = $this->messages->countUnread($userId);

        return new JsonResponse($count);
    }

    /**
     * POST   /api/user/messages/{message}/reply
     *
     * @param \App\Http\Requests\Message\Reply $request
     * @param  int                             $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reply(Reply $request, int $id): JsonResponse
    {
        $message = $this->messages->find($id);

        $details = $request->only(['subject', 'body', 'importance_level']);

        $details['from_id'] = $request->user()->id;
        $details['from_name'] = $request->user()->name;

        $details['to_name'] = $message->from_name;
        $details['to_id'] = $message->from_id;
        $details['reply'] = $message->id;
        $details['reply_count'] = $message->reply_count + 1;
        $details['payload'] = [];

        $details['type'] = Message::USER_MESSAGE;
        $details['importance_level'] = $details['importance_level'] ?: 10;

        $newMessage = $this->messages->create($details);

        return new JsonResponse($newMessage);
    }

    /**
     * POST   /api/user/messages
     *
     * @param \App\Http\Requests\Message\Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request)
    {
        $recipient = $this->users->find($request->to);

        $details = $request->only(['subject', 'body', 'importance_level']);
        $details['to_id'] = $recipient->id;
        $details['to_name'] = $recipient->name;

        $details['from_id'] = $request->user()->id;
        $details['from_name'] = $request->user()->name;
        $details['type'] = Message::USER_MESSAGE;
        $details['payload'] = [];

        $details['importance_level'] = $details['importance_level'] ?: 10;

        $message = $this->messages->create($details);

        return new JsonResponse($message);
    }
}
