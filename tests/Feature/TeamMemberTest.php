<?php namespace Tests\Feature;

use App\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class TeamMemberTest
 *
 * @package Tests\Feature
 */
class TeamMemberTest extends TestCase
{
    use RefreshDatabase;

    function testCanGetTeamMembersList()
    {
        $admin = $this->createSuperAdmin();
        $teams = factory(\App\Team::class, 2)->create();

        // Members for other team, to make sure that we get only required ones.
        factory(\App\TeamMember::class, 2)
            ->create(['team_id' => $teams[0]->id]);

        $members = factory(\App\TeamMember::class, 2)
            ->create(['team_id' => $teams[1]->id]);

        $response = $this
            ->actingAs($admin, 'api')
            ->get("/api/teams/{$teams[1]->id}/members");

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 2,
                'data' => [
                    [
                        'name' => $members[1]->name,
                        'team_id' => $members[1]->team_id,
                        'user_id' => $members[1]->user_id,
                        'membership_type' => $members[1]->membership_type,
                        'id' => $members[1]->id,
                    ], [
                        'name' => $members[0]->name,
                        'team_id' => $members[0]->team_id,
                        'user_id' => $members[0]->user_id,
                        'membership_type' => $members[0]->membership_type,
                        'id' => $members[0]->id,
                    ],
                ],
            ]);
    }

    function testCanGetTeamMember()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();
        $member = factory(\App\TeamMember::class)->create([
            'team_id' => $team->id,
            'user_id' => $admin->id,
        ]);

        $url = "/api/teams/{$team->id}/members/{$member->id}";
        $response = $this->actingAs($admin, 'api')->get($url);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $member->id,
                'name' => $member->name,
                'team_id' => $member->team_id,
                'user_id' => $member->user_id,
                'membership_type' => $member->membership_type,
            ]);
    }

    function testCanGetCorrectMember()
    {
        $admin = $this->createSuperAdmin();
        $teams = factory(\App\Team::class, 2)->create();
        $members = factory(\App\TeamMember::class, 2)->create([
            'team_id' => $teams[0]->id,
            'user_id' => $admin->id,
        ]);

        $url = "/api/teams/{$teams[0]->id}/members/{$members[1]->id}";
        $response = $this->actingAs($admin, 'api')->get($url);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $members[1]->id,
                'name' => $members[1]->name,
                'team_id' => $members[1]->team_id,
                'user_id' => $members[1]->user_id,
                'membership_type' => $members[1]->membership_type,
            ]);
    }

    function testCanStoreNewMemberForTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();
        $url = "/api/teams/{$team->id}/members";

        $response = $this->actingAs($admin, 'api')->postJson($url, [
            'name' => 'New member name',
            'user_id' => 0,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => 'New member name',
                'team_id' => $team->id,
                'user_id' => null,
                'membership_type' => 'member',
            ]);
    }

    function testCanStoreNewMemberWithUserReferenceForTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();
        $url = "/api/teams/{$team->id}/members";

        $response = $this
            ->actingAs($admin, 'api')
            ->postJson($url, [
                'name' => 'New member name',
                'user_id' => $admin->id,
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => 'New member name',
                'team_id' => $team->id,
                'user_id' => $admin->id,
                'membership_type' => 'invited',
            ]);

        $payloadTeam = '"from_team_name":"' . $team->name . '"';
        $payloadUser = '"from_user_name":"' . $admin->name . '"';
        $payloadMember = '"member_id":' . $response->json()['id'];
        $completed = '"completed":false';
        $payload = "{{$payloadTeam},{$payloadUser},{$payloadMember},{$completed}}";
        $this->assertDatabaseHas('messages', [
            'subject' => "{$admin->name} has invited you to join {$team->name} team",
            'importance_level' => 7,
            'to_id' => $admin->id,
            'to_name' => $admin->name,
            'from_id' => $admin->id,
            'from_name' => $admin->name,
            'type' => Message::TEAM_MEMBER_INVITATION,
            'payload' => $payload,
        ]);
    }

    function testCanUpdateTeamMember()
    {
        $admin = $this->createSuperAdmin();
        $user = $this->createPostManager();
        $team = factory(\App\Team::class, 2)->create()[0];

        factory(\App\TeamMember::class)->create([
            'team_id' => $team->id,
            'user_id' => $admin->id,
        ]);

        $member = factory(\App\TeamMember::class)->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $url = "/api/teams/{$team->id}/members/{$member->id}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'name' => 'New Member Name',
            'user_id' => $member->user_id,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $member->id,
                'name' => 'New Member Name',
                'team_id' => $member->team_id,
                'user_id' => $member->user_id,
                'membership_type' => $member->membership_type,
            ]);

        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'name' => 'New Member Name',
        ]);
    }

    function testCanUpdateTeamMemberWithNewUserInvitation()
    {
        $admin = $this->createSuperAdmin();
        $user1 = $this->createPostManager();
        $user2 = $this->createUser();
        $team = factory(\App\Team::class, 2)->create()[0];

        factory(\App\TeamMember::class)->create([
            'team_id' => $team->id,
            'user_id' => $admin->id,
        ]);

        $member = factory(\App\TeamMember::class)->create([
            'team_id' => $team->id,
            'user_id' => $user1->id,
            'membership_type' => \App\TeamMember::MEMBER,
        ]);

        $url = "/api/teams/{$team->id}/members/{$member->id}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'name' => 'New Member Name',
            'user_id' => $user2->id, // adding new value here
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $member->id,
                'name' => 'New Member Name',
                'team_id' => $member->team_id,
                'user_id' => $user2->id,
                'membership_type' => \App\TeamMember::INVITED,
            ]);

        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'name' => 'New Member Name',
        ]);

        $payloadTeam = '"from_team_name":"' . $team->name . '"';
        $payloadUser = '"from_user_name":"' . $admin->name . '"';
        $payloadMember = '"member_id":' . $response->json()['id'];
        $completed = '"completed":false';
        $payload = "{{$payloadTeam},{$payloadUser},{$payloadMember},{$completed}}";
        $this->assertDatabaseHas('messages', [
            'subject' => "{$admin->name} has invited you to join {$team->name} team",
            'importance_level' => "7",
            'to_id' => $user2->id,
            'to_name' => $user2->name,
            'from_id' => $admin->id,
            'from_name' => $admin->name,
            'type' => Message::TEAM_MEMBER_INVITATION,
            'payload' => $payload,
        ]);
    }
}
