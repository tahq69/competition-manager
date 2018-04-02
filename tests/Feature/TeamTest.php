<?php namespace Tests\Feature;

use App\TeamMember;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class TeamTest
 * @package Tests\Feature
 */
class TeamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic team list request.
     * @return void
     */
    public function testCanGetTeamsList()
    {
        $admin = $this->createSuperAdmin();
        $teams = factory(\App\Team::class, 2)->create();

        $response = $this
            ->actingAs($admin, 'api')
            ->get('/api/teams');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'name' => $teams[1]->name,
                        'short' => $teams[1]->short,
                        'id' => $teams[1]->id,
                    ], [
                        'name' => $teams[0]->name,
                        'short' => $teams[0]->short,
                        'id' => $teams[0]->id,
                    ],
                ],
            ]);
    }

    /**
     * Pagination result of team list request
     * @return void
     */
    public function testCanGetPagingTeamsList()
    {
        $admin = $this->createSuperAdmin();
        $teams = factory(\App\Team::class, 4)->create();

        $response = $this
            ->actingAs($admin, 'api')
            ->get('/api/teams?sort_by=id&sort_direction=asc&per_page=2&page=2');

        $response
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 2,
                'total' => 4,
                'data' => [
                    [
                        'name' => $teams[2]->name,
                        'short' => $teams[2]->short,
                        'id' => $teams[2]->id,
                    ],
                    [
                        'name' => $teams[3]->name,
                        'short' => $teams[3]->short,
                        'id' => $teams[3]->id,
                    ],
                ],
            ]);
    }

    /**
     * Pagination result of team list request with filtered only managed teams.
     * @return void
     */
    public function testCantGetNonManagedTeamsWhenFiltering()
    {
        $owner = $this->createTeamOwner();
        $admin = $this->createSuperAdmin();

        $team = $this->createTeam([$owner]);
        $this->createTeam([$admin]);

        $response = $this->actingAs($owner, 'api')->get('/api/teams?managed=1');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 1,
                'data' => [[
                    'name' => $team->name,
                    'short' => $team->short,
                    'id' => $team->id,
                ],],
            ]);
    }

    /**
     * A basic team request test.
     * @return void
     */
    function testCanGetTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();

        $url = "/api/teams/{$team->id}";
        $response = $this->actingAs($admin, 'api')->get($url);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'name' => $team->name,
                'short' => $team->short,
            ]);
    }

    /**
     * A basic team create request test.
     * @return void
     */
    function testCanCreateNewTeam()
    {
        $owner = $this->createTeamManager();

        $response = $this
            ->actingAs($owner, 'api')
            ->postJson('/api/teams', [
                'name' => 'New team',
                'short' => 'ntm',
                'logo' => '/logo.png',
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => 'New team',
                'short' => 'ntm',
            ]);

        $this->assertDatabaseHas('team_members', [
            'user_id' => $owner->id,
            'team_id' => $response->json()['id'],
            'membership_type' => TeamMember::MANAGER,
        ]);
    }

    function testCantCreateNewTeamWithExistingName()
    {
        $manager = $this->createTeamManager();
        factory(\App\Team::class)->create([
            'name' => 'existing name',
            'short' => 'en',
        ]);

        $response = $this
            ->actingAs($manager, 'api')
            ->postJson('/api/teams', [
                'name' => 'existing name',
                'short' => 'en',
            ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => [],
                    'short' => [],
                ]
            ]);
    }

    /**
     * A basic team update request test.
     * @return void
     */
    function testCanUpdateTeam()
    {
        $admin = $this->createSuperAdmin();
        $team = factory(\App\Team::class)->create();

        $url = "/api/teams/{$team->id}";
        $response = $this->actingAs($admin, 'api')->putJson($url, [
            'id' => $team->id,
            'name' => 'updated name',
            'short' => 'updated short',
            'logo' => 'updated/logo.png',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'name' => 'updated name',
                'short' => 'updated short',
                'logo' => 'updated/logo.png',
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'updated name',
            'short' => 'updated short',
            'id' => $team->id,
        ]);
    }
}
