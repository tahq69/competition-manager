<?php namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class TeamMemberTest
 * @package Tests\Feature
 */
class TeamMemberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic team members list request.
     * @return void
     */
    public function testCanGetTeamMembersList()
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

    /**
     * A basic team member request test.
     * @return void
     */
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

    /**
     * A team member request test where team has multiple members and we
     * requesting not first one.
     * @return void
     */
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

    /**
     * A basic team member store request test.
     * @return void
     */
    function testCanStoreNewMemberForTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();
        $url = "/api/teams/{$team->id}/members";

        $response = $this->actingAs($admin, 'api')->postJson($url, [
            'name' => 'New member name',
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

    /**
     * A team member store request test where user identifier is presented.
     * @return void
     */
    function testCanStoreNewMemberWithUserReferenceForTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();
        $url = "/api/teams/{$team->id}/members";

        $response = $this->actingAs($admin, 'api')->postJson($url, [
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
    }

    /**
     * A basic team member update request test.
     * @return void
     */
    function testCanUpdateTeamMember()
    {
        $admin = $this->createSuperAdmin();
        $teams = factory(\App\Team::class, 2)->create();
        $members = factory(\App\TeamMember::class, 2)->create([
            'team_id' => $teams[0]->id,
            'user_id' => $admin->id,
        ]);

        $url = "/api/teams/{$teams[0]->id}/members/{$members[1]->id}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'name' => 'New Member Name',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $members[1]->id,
                'name' => 'New Member Name',
                'team_id' => $members[1]->team_id,
                'user_id' => $members[1]->user_id,
                'membership_type' => $members[1]->membership_type,
            ]);

        $this->assertDatabaseHas('team_members', [
            'id' => $members[1]->id,
            'name' => 'New Member Name',
        ]);
    }
}
