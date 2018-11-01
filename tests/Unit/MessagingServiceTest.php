<?php namespace Tests\Unit;

use App\Contracts;
use App\Services\MessagingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class MessagingServiceTest
 *
 * @group   Message
 * @package Tests\Unit
 */
class MessagingServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test properly prepares invitation message data.
     *
     * @group  Message_SendTeamMemberInvitation
     * @return void
     */
    public function testSendTeamMemberInvitation_ProperlyPreparesDataInMessage()
    {
        \App\User::unguard();
        \App\TeamMember::unguard();

        // Arrange
        $messages = \Mockery::mock(Contracts\IMessageRepository::class);
        $users = \Mockery::mock(Contracts\IUserRepository::class);
        $team = new \App\Team(['name' => 'team_name']);
        $member = new \App\TeamMember(['id' => 123]);
        $sender = new \App\User(['name' => 'sender', 'id' => 234]);
        $recipient = new \App\User(['name' => 'recipient', 'id' => 345]);
        $message = new \App\Message([]);
        $sut = new MessagingService($messages, $users);

        // Mock
        $users->shouldReceive('find')->times(1)->with($sender->id, ['id', 'name'])->andReturn($sender);
        $users->shouldReceive('find')->times(1)->with($recipient->id, ['id', 'name'])->andReturn($recipient);

        $messages->shouldReceive('create')->times(1)->with(\Mockery::on(function ($details) {
            $this->assertEquals([
                'subject' => 'sender has invited you to join team_name team',
                'body' => 'TEAM_MEMBER_INVITATION',
                'importance_level' => 7,
                'to_id' => 345,
                'to_name' => 'recipient',
                'from_id' => 234,
                'from_name' => 'sender',
                'type' => 'TEAM_MEMBER_INVITATION',
                'payload' => [
                    'from_team_name' => 'team_name',
                    'from_user_name' => 'sender',
                    'member_id' => 123,
                    'completed' => false,
                ],
            ], $details);

            return true;
        }))->andReturn($message);

        // Act
        $result = $sut->sendTeamMemberInvitation($sender->id, $recipient->id, $team, $member);

        // Assert
        $this->assertNotEmpty($result);
        $this->assertEquals($message, $result);

        \App\User::reguard();
        \App\TeamMember::reguard();
    }
}
