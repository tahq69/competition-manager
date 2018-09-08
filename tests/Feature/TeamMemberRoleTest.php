<?php namespace Tests\Feature;

use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class TeamMemberRoleTest
 * @package Tests\Feature
 */
class TeamMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetOwnTeamMemberRolesList()
    {
        $user = $this->createUser();
        $teamId = $this->createTeam()->id;
        $memberId = $this->createTeamManager($teamId, $user->id)->id;

        $response = $this
            ->actingAs($user, 'api')
            ->get("/api/teams/{$teamId}/members/{$memberId}/roles");

        $response
            ->assertStatus(200)
            ->assertJson([
                'MANAGE_TEAMS',
                'MANAGE_MEMBERS',
                'MANAGE_MEMBER_ROLES',
            ]);
    }

    public function testSimpleUserCanNOTGetTeamMemberRolesList()
    {
        $user = $this->createUser();
        $teamId = $this->createTeam()->id;
        $memberId = $this->createTeamManager($teamId)->id;

        $response = $this
            ->actingAs($user, 'api')
            ->get("/api/teams/{$teamId}/members/{$memberId}/roles");

        $response->assertStatus(403);
    }

    public function testManagerCanAssignTeamMemberRoles()
    {
        $user = $this->createUser();
        $teamId = $this->createTeam()->id;
        $memberId = $this->createTeamManager($teamId, $user->id)->id;

        $response = $this
            ->actingAs($user, 'api')
            ->postJson("/api/teams/{$teamId}/members/{$memberId}/roles", [
                'roles' => [
                    'MANAGE_TEAMS',
                    'MANAGE_MEMBERS',
                    'DUMMY_ONE',
                ]
            ]);

        $response
            ->assertStatus(200)
            ->assertSee('true');

        $manageTeamsId = Role::where('key', 'MANAGE_TEAMS')->first()->id;
        $this->assertDatabaseHas('team_member_role', [
            'role_id' => $manageTeamsId,
            'team_member_id' => $memberId
        ]);

        $manageMembersId = Role::where('key', 'MANAGE_MEMBERS')->first()->id;
        $this->assertDatabaseHas('team_member_role', [
            'role_id' => $manageMembersId,
            'team_member_id' => $memberId
        ]);
    }
}