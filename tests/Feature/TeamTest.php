<?php namespace Tests\Feature;

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
}
