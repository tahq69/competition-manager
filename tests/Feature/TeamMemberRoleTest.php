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
}