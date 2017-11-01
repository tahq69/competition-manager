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
                        'name' => $teams[0]->name,
                        'short' => $teams[0]->short,
                        'id' => $teams[0]->id,
                    ], [
                        'name' => $teams[1]->name,
                        'short' => $teams[1]->short,
                        'id' => $teams[1]->id,
                    ],
                ],
            ]);
    }
}
