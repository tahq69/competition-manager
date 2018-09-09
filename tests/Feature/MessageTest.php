<?php namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class MessageTest
 *
 * @group   Message
 * @package Tests\Feature
 */
class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthorized user access to guarded route.
     *
     * @group  Message_Index
     * @return void
     */
    function testIndex_UnauthorizedUserCantGetMessages()
    {
        // Arrange
        $url = "/api/user/messages";

        // Act
        $response = $this->getJson($url);

        // Assert
        $response->assertStatus(401);
    }

    /**
     * Test authorized user can access empty message box.
     *
     * @group  Message_Index
     * @return void
     */
    function testIndex_AuthorizedUserCanAccessMessages()
    {
        // Arrange
        $user = $this->createUser();
        $url = "/api/user/messages";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200);
    }

    /**
     * Test authorized user can get message from inbox.
     *
     * @group  Message_Index
     * @return void
     */
    function testIndex_UserCanGetInboxMessage()
    {
        // Arrange
        $user = $this->createUser();
        $msg = $this->createInboxMessage($user);
        $url = "/api/user/messages";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson([
            'total' => 1,
            'data' => [[
                'to_id' => $user->id,
                'to_name' => $user->name,
                'body' => $msg->body,
                'from_id' => $msg->from_id,
                'from_name' => $msg->from_name,
                'importance_level' => $msg->importance_level,
                'is_read' => (bool)$msg->is_read,
                'reply' => $msg->reply,
                'reply_count' => $msg->reply_count,
                'subject' => $msg->subject,
                'type' => $msg->type,
            ]],
        ]);
    }

    /**
     * Test authorized user cant get message from other user inbox.
     *
     * @group  Message_Index
     * @return void
     */
    function testIndex_UserCantGetOtherUserInboxMessage()
    {
        // Arrange
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $msg = $this->createInboxMessage($user1);
        $url = "/api/user/messages";

        // Act
        $response = $this->actingAs($user2, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson(['total' => 0]);
    }

    /**
     * Test authorized user can get message from outbox.
     *
     * @group  Message_Index
     * @return void
     */
    function testIndex_UserCanGetOutboxMessage()
    {
        // Arrange
        $otherUser = $this->createUser();
        $this->createOutboxMessage($otherUser);
        $this->createInboxMessage($otherUser);

        $user = $this->createUser();
        $msg = $this->createOutboxMessage($user);
        $url = "/api/user/messages?type=outbox";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson([
            'total' => 1,
            'data' => [[
                'from_id' => $user->id,
                'from_name' => $user->name,
                'to_id' => $msg->to_id,
                'to_name' => $msg->to_name,
                'body' => $msg->body,
                'importance_level' => $msg->importance_level,
                'is_read' => (bool)$msg->is_read,
                'reply' => $msg->reply,
                'reply_count' => $msg->reply_count,
                'subject' => $msg->subject,
                'type' => $msg->type,
            ]],
        ]);
    }

    /**
     * Test User can read inbox message.
     *
     * @group  Message_Read
     * @return void
     */
    function testRead_UserCanReadInboxMessage()
    {
        // Arrange
        $user = $this->createUser();
        $msg = $this->createInboxMessage($user);
        $url = "/api/user/messages/read/{$msg->id}";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson([
            'to_id' => $user->id,
            'to_name' => $user->name,
            'from_id' => $msg->from_id,
            'from_name' => $msg->from_name,
            'body' => $msg->body,
            'importance_level' => $msg->importance_level,
            'reply' => $msg->reply,
            'reply_count' => $msg->reply_count,
            'subject' => $msg->subject,
            'type' => $msg->type,
        ]);
    }

    /**
     * Test User can read outbox message.
     *
     * @group  Message_Read
     * @return void
     */
    function testRead_UserCanReadOutboxMessage()
    {
        // Arrange
        $user = $this->createUser();
        $msg = $this->createOutboxMessage($user);
        $url = "/api/user/messages/read/{$msg->id}";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson([
            'to_id' => $msg->to_id,
            'to_name' => $msg->to_name,
            'from_id' => $user->id,
            'from_name' => $user->name,
            'body' => $msg->body,
            'importance_level' => $msg->importance_level,
            'is_read' => $msg->is_read,
            'reply' => $msg->reply,
            'reply_count' => $msg->reply_count,
            'subject' => $msg->subject,
            'type' => $msg->type,
        ]);
    }

    /**
     * Test user read inbox message updates its status.
     *
     * @group  Message_Read
     * @return void
     */
    function testRead_UserReadInboxMessageUpdatesMessageStatus()
    {
        // Arrange
        $user = $this->createUser();
        $msg = $this->createInboxMessage($user, false);
        $url = "/api/user/messages/read/{$msg->id}";

        // Act
        $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $this->assertDatabaseHas('messages', [
            'id' => $msg->id,
            'is_read' => true,
        ]);
    }

    /**
     * Test user read outbox message does NOT updates its status.
     *
     * @group  Message_Read
     * @return void
     */
    function testRead_UserReadOutboxMessageDoesNOTUpdatesMessageStatus()
    {
        // Arrange
        $user = $this->createUser();
        $msg = $this->createOutboxMessage($user, false);
        $url = "/api/user/messages/read/{$msg->id}";

        // Act
        $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $this->assertDatabaseHas('messages', [
            'id' => $msg->id,
            'is_read' => false,
        ]);
    }

    /**
     * Test user read inbox message returns message tree.
     *
     * @group  Message_Read
     * @return void
     */
    function testRead_UserReadInboxMessageWithReplays()
    {
        // Arrange
        $reader = $this->createUser();
        $sender = $this->createUser();
        $msg = $this->createInboxMessage($sender, false);
        $reply = $this->createReplyMessage($msg, $reader);
        $url = "/api/user/messages/read/{$reply->id}";

        // Act
        $response = $this->actingAs($reader, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertJson([
            'id' => $reply->id,
            'to_id' => $reader->id,
            'to_name' => $reader->name,
            'reply_on' => [
                'id' => $msg->id,
                'to_id' => $sender->id,
                'to_name' => $sender->name,
            ],
        ]);
    }

    /**
     * Test count unread messages.
     *
     * @group  Message_CountUnread
     * @return void
     */
    function testCountUnread_ReturnsCorrectCountOfUnreadMessages()
    {

        // Arrange
        $user1 = $this->createUser();
        $this->createInboxMessage($user1, false);
        $this->createInboxMessage($user1, true);

        $user2 = $this->createUser();
        $this->createInboxMessage($user2, false);
        $this->createInboxMessage($user2, true);
        $this->createInboxMessage($user2, false);
        $this->createInboxMessage($user2, true);

        $url = "/api/user/messages/count/unread";

        // Act
        $response = $this->actingAs($user2, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200)->assertSee("2");
    }

    /**
     * Test unauthorized user cant send new message.
     *
     * @group  Message_Store
     * @return void
     */
    function testStore_UnauthorizedUserCantSendMessage()
    {
        // Assert
        $recipient = $this->createUser();
        $url = "/api/user/messages";
        $msg = [
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 1,
            'to' => $recipient->id,
        ];

        // Act
        $response = $this->postJson($url, $msg);

        // Assert
        $response->assertStatus(401);
    }

    /**
     * Test user can send new message.
     *
     * @group  Message_Store
     * @return void
     */
    function testStore_UserCanSendMessage()
    {
        // Assert
        $sender = $this->createUser();
        $recipient = $this->createUser();
        $url = "/api/user/messages";
        $msg = [
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 1,
            'to' => $recipient->id,
        ];

        // Act
        $response = $this->actingAs($sender, 'api')->postJson($url, $msg);

        // Assert
        $response->assertStatus(200)->assertJson([
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 1,
            'to_id' => $recipient->id,
        ]);

        $this->assertDatabaseHas('messages', [
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 1,
            'to_id' => $recipient->id,
            'from_id' => $sender->id,
            'from_name' => $sender->name,
            'to_name' => $recipient->name,
        ]);
    }

    /**
     * Test user can send reply message.
     *
     * @group  Message_Reply
     * @return void
     */
    function testReply_UserCanSendReplyMessage()
    {
        // Assert
        $sender = $this->createUser();
        $incomingMsg = $this->createInboxMessage($sender, true);

        $url = "/api/user/messages/{$incomingMsg->id}/reply";
        $msg = [
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 2,
        ];

        // Act
        $response = $this->actingAs($sender, 'api')->postJson($url, $msg);

        // Assert
        $response->assertStatus(200)->assertJson([
            'from_id' => $sender->id,
            'from_name' => $sender->name,
            'to_id' => $incomingMsg->from_id,
            'to_name' => $incomingMsg->from_name,
            'reply' => $incomingMsg->id,
            'reply_count' => 1,
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 2,
        ]);

        $this->assertDatabaseHas('messages', [
            'from_id' => $sender->id,
            'from_name' => $sender->name,
            'to_id' => $incomingMsg->from_id,
            'to_name' => $incomingMsg->from_name,
            'reply' => $incomingMsg->id,
            'reply_count' => 1,
            'subject' => 'message_subject',
            'body' => 'message_body',
            'importance_level' => 2,
        ]);
    }

    private function createInboxMessage(\App\User $user, bool $isRead = false): \App\Message
    {
        $message = factory(\App\Message::class)->create([
            'type' => \App\Message::USER_MESSAGE,
            'to_id' => $user->id,
            'to_name' => $user->name,
            'is_read' => $isRead,
        ]);

        return $message;
    }

    private function createOutboxMessage(\App\User $user, bool $isRead = false): \App\Message
    {
        $message = factory(\App\Message::class)->create([
            'type' => \App\Message::USER_MESSAGE,
            'from_id' => $user->id,
            'from_name' => $user->name,
            'is_read' => $isRead,
        ]);

        return $message;
    }

    private function createReplyMessage(\App\Message $msg, \App\User $user): \App\Message
    {
        $message = factory(\App\Message::class)->create([
            'type' => \App\Message::USER_MESSAGE,
            'to_id' => $user->id,
            'to_name' => $user->name,
            'reply' => $msg->id,
        ]);

        return $message;
    }
}
