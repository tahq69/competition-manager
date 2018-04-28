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

    public function testCanGetTeamMemberRolesList()
    {
        $user = factory(\App\User::class)->create();
        $teamId = factory(\App\Team::class)->create()->id;
        $memberId = $this->createTeamMemberManager($teamId)->id;

        // Add required roles to user what will make role list method
        // accessible for him.
        $this->createTeamMemberManager($teamId, $user->id);

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
        $teamId = factory(\App\Team::class)->create()->id;
        $memberId = $this->createTeamMemberManager($teamId)->id;

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
        $memberId = factory(\App\TeamMember::class)
            ->create(['team_id' => $teamId])
            ->id;

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
            ->assertSee("true");

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