<?php namespace Tests\Feature;

use App\Area;
use App\Contracts\MemberRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AreaTest
 *
 * @package Tests\Feature
 */
class AreaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition area list request.
     *
     * @return void
     */
    public function testCanGetAreaList()
    {
        factory(\App\User::class)->create();
        factory(\App\Area::class);
        $competition = factory(\App\Competition::class)->create()->id;
        $areas = factory(\App\Area::class)
            ->times(2)
            ->create(['competition_id' => $competition]);
        factory(\App\Area::class);

        $response = $this->get("/api/competitions/{$competition}/areas");

        $response
            ->assertStatus(200)
            ->assertJson([
                [
                    'competition_id' => $competition,
                    'type' => $areas[1]->type,
                    'nr' => $areas[1]->nr,
                    'title' => $areas[1]->title,
                    'id' => $areas[1]->id,
                ], [
                    'competition_id' => $competition,
                    'type' => $areas[0]->type,
                    'nr' => $areas[0]->nr,
                    'title' => $areas[0]->title,
                    'id' => $areas[0]->id,
                ],
            ]);
    }

    /**
     * A basic competition area store request.
     *
     * @return void
     */
    public function testCanStoreArea()
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::MANAGE_COMPETITION_AREAS]);
        $competition = $this->createCompetition($team->id);
        $cm = $competition->id;
        $this->createArea($team->id, $cm);

        /** @var \App\Area $area */
        $area = factory(Area::class)->make();

        $details = [
            'competition_id' => $cm,
            'title' => 'area title - dashed',
            'type' => $area->type,
            'nr' => $area->nr,
            'description' => $area->description,
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson("/api/competitions/{$cm}/areas", $details);

        $response
            ->assertStatus(200)
            ->assertJson($details);

        $this->assertDatabaseHas('areas', [
            'created_by' => $user->id,
            'competition_id' => $cm,
            'title' => 'area title - dashed',
            'type' => $area->type,
            'nr' => $area->nr,
        ]);
    }

    /**
     * A basic competition area request.
     *
     * @return void
     */
    public function testCanGetArea()
    {
        $area = $this->createArea();
        $cm = $area->competition_id;

        $response = $this->get("/api/competitions/{$cm}/areas/{$area->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cm,
                'type' => $area->type,
                'nr' => $area->nr,
                'title' => $area->title,
                'id' => $area->id,
            ]);
    }

    /**
     * A basic competition update request test.
     *
     * @return void
     */
    public function testCanUpdateArea()
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::MANAGE_COMPETITION_AREAS]);
        $competition = $this->createCompetition($team->id);
        $cm = $competition->id;
        $area = $this->createArea($team->id, $cm);

        $details = [
            'id' => $area->id,
            'competition_id' => $cm,
            'title' => 'updated',
            'type' => Area::CAGE,
            'nr' => 99,
            'description' => 'description',
        ];

        $response = $this
            ->actingAs($user, 'api')
            ->patchJson("/api/competitions/{$cm}/areas/{$area->id}", $details);

        $response
            ->assertStatus(200)
            ->assertJson($details);

        $this->assertDatabaseHas('areas', array_merge($details, [
            'updated_by' => $user->id,
        ]));
    }

    /**
     * A basic competition destroy request test.
     *
     * @return void
     */
    public function testCanDestroyArea()
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::MANAGE_COMPETITION_AREAS]);
        $competition = $this->createCompetition($team->id);
        $cm = $competition->id;
        $area = $this->createArea($team->id, $cm);

        $response = $this
            ->actingAs($user, 'api')
            ->delete("/api/competitions/{$cm}/areas/{$area->id}");

        $response
            ->assertStatus(200)
            ->assertSee('true');

        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }
}
