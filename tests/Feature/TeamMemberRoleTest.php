<?php namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class TeamMemberRoleTest
 * @package Tests\Feature
 */
class TeamMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetTeamMemberRolesList()
    {
        $user = factory(\App\User::class)->create();

        $teams = factory(\App\Team::class, 2)->create();
        $teamId = $teams[1]->id;

        // Members for other team, to make sure that we get only required ones.
        factory(\App\TeamMember::class, 2)
            ->create(['team_id' => $teams[0]->id]);

        $this->createTeamMemberManager($teamId, $user->id);
        $member = $this->createTeamMemberManager($teamId);
        $memberId = $member->id;

        $response = $this
            ->actingAs($user, 'api')
            ->get("/api/teams/{$teamId}/members/{$memberId}/roles");

        $response
            ->assertStatus(200)
            ->assertJson([
                "MANAGE_TEAMS",
                "MANAGE_MEMBERS",
                "MANAGE_MEMBER_ROLES",
            ]);
    }

    public function testSimpleUserCantGetTeamMemberRolesList()
    {
        $user = factory(\App\User::class)->create();

        $team = factory(\App\Team::class)->create();
        $teamId = $team->id;

        $member = $this->createTeamMemberManager($teamId);
        $memberId = $member->id;

        $response = $this
            ->actingAs($user, 'api')
            ->get("/api/teams/{$teamId}/members/{$memberId}/roles");

        $response->assertStatus(403);
    }

    public function testCanAssignTeamMemberRoles()
    {
        $user = factory(\App\User::class)->create();
        $teamId = factory(\App\Team::class)->create()->id;

        // Add required roles to user what will make role update method
        // accessible for him.
        $this->createTeamMemberManager($teamId, $user->id);

        // Clear member instance in same team as user updating it.
        $memberId = factory(\App\TeamMember::class)->create(['team_id' => $teamId])->id;

        $response = $this
            ->actingAs($user, 'api')
            ->postJson("/api/teams/{$teamId}/members/{$memberId}/roles", [
                "roles" => [
                    "MANAGE_TEAMS",
                    "MANAGE_MEMBERS",
                    "DUMMY_ONE",
                ]
            ]);

        $response
            ->assertStatus(200)
            ->assertSee("true");

        $this->assertDatabaseHas('team_member_role', [
            'role_id' => 5,
            'team_member_id' => $memberId
        ]);

        $this->assertDatabaseHas('team_member_role', [
            'role_id' => 6,
            'team_member_id' => $memberId
        ]);
    }
}