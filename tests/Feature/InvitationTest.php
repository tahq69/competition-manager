<?php namespace Tests\Feature;

use App\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class InvitationTest
 *
 * @group   Invitation
 * @package Tests\Feature
 */
class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthorized user access to guarded route.
     *
     * @group  Invitation_ConfirmMember
     * @return void
     */
    function testConfirmMember_UnauthorizedUserCantConfirmInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/confirm";

        // Act
        $response = $this->getJson($url);

        // Assert
        $response->assertStatus(401);
    }

    /**
     * Test authorized user cant access other user invitation.
     *
     * @group  Invitation_ConfirmMember
     * @return void
     */
    function testConfirmMember_UserCantConfirmOtherUserInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $user = factory(\App\User::class)->create();
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/confirm";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * Test authorized user cant confirm confirmed message.
     *
     * @group  Invitation_ConfirmMember
     * @return void
     */
    function testConfirmMember_UserCantConfirmConfirmedInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember(\App\TeamMember::MEMBER);
        $invitation = $this->createMemberInvitation($member);
        $user = \App\User::find($member->user_id);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/confirm";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * Test user can confirm valid invitation.
     *
     * @group  Invitation_ConfirmMember
     * @return void
     */
    function testConfirmMember_UserCanConfirmInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $user = \App\User::find($member->user_id);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/confirm";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'membership_type' => \App\TeamMember::MEMBER,
            'user_id' => $member->user_id,
        ]);

        $this->assertDatabaseHas((new \App\Message())->getTable(), [
            'id' => $invitation->id,
            'payload' =>
                '{"from_team_name":"' . $invitation->payload['from_team_name'] .
                '","from_user_name":"' . $invitation->payload['from_user_name'] .
                '","member_id":' . $invitation->payload['member_id'] .
                ',"completed":true}',
        ]);
    }

    /**
     * Test unauthorized user access to guarded route.
     *
     * @group  Invitation_RefuseMember
     * @return void
     */
    function testRefuseMember_UnauthorizedUserCantRefuseInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/refuse";

        // Act
        $response = $this->getJson($url);

        // Assert
        $response->assertStatus(401);
    }

    /**
     * Test authorized user cant access other user invitation.
     *
     * @group  Invitation_RefuseMember
     * @return void
     */
    function testRefuseMember_UserCantRefuseOtherUserInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $user = factory(\App\User::class)->create();
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/refuse";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * Test authorized user cant confirm confirmed message.
     *
     * @group  Invitation_RefuseMember
     * @return void
     */
    function testRefuseMember_UserCantRefuseConfirmedInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember(\App\TeamMember::MEMBER);
        $invitation = $this->createMemberInvitation($member);
        $user = \App\User::find($member->user_id);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/refuse";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * Test user can confirm valid invitation.
     *
     * @group  Invitation_RefuseMember
     * @return void
     */
    function testRefuseMember_UserCanRefuseInvitation()
    {
        // Arrange
        $member = $this->createInvitedMember();
        $invitation = $this->createMemberInvitation($member);
        $user = \App\User::find($member->user_id);
        $url = "/api/invitations/{$invitation->id}/team-member/{$member->id}/refuse";

        // Act
        $response = $this->actingAs($user, 'api')->getJson($url);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'membership_type' => \App\TeamMember::MEMBER,
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('messages', [
            'from_id' => $invitation->to_id,
            'to_id' => $invitation->from_id,
            'reply' => $invitation->id,
            'type' => \App\Message::USER_MESSAGE,
        ]);

        $this->assertDatabaseHas((new \App\Message())->getTable(), [
            'id' => $invitation->id,
            'payload' =>
                '{"from_team_name":"' . $invitation->payload['from_team_name'] .
                '","from_user_name":"' . $invitation->payload['from_user_name'] .
                '","member_id":' . $invitation->payload['member_id'] .
                ',"completed":true}',
        ]);
    }

    private function createInvitedMember(
        string $membership = \App\TeamMember::INVITED
    ): \App\TeamMember
    {
        $user = factory(\App\User::class)->times(2)->create()[1];
        return factory(\App\TeamMember::class)->times(2)->create([
            'membership_type' => $membership,
            'user_id' => $user->id,
        ])[1];
    }

    private function createMemberInvitation(
        \App\TeamMember $member
    ): \App\Message
    {
        $from = factory(\App\User::class)->create();
        $team = Team::find($member->team_id);
        $svc = app(\App\Services\MessagingService::class);

        return $svc->sendTeamMemberInvitation($from->id, $member->user_id, $team, $member);
    }
}
