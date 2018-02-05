<?php namespace Tests\Feature;

use App\Competition;
use App\Discipline;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class DisciplineTest
 * @package Tests\Feature
 */
class DisciplineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition disciplines list request.
     * @return void
     */
    public function testCanGetCompetitionDisciplineList()
    {
        $admin = $this->createSuperAdmin();
        $disciplines = $this->createDisciplines(3);
        $cmId = $disciplines[0]->competition_id;

        $response = $this->get("/api/competitions/{$cmId}/disciplines");

        $response
            ->assertStatus(200)
            ->assertJson([[
                'competition_id' => $cmId,
                'title' => $disciplines[2]->title,
                'short' => $disciplines[2]->short,
                'type' => $disciplines[2]->type,
                'id' => $disciplines[2]->id,
            ], [
                'competition_id' => $cmId,
                'title' => $disciplines[1]->title,
                'short' => $disciplines[1]->short,
                'type' => $disciplines[1]->type,
                'id' => $disciplines[1]->id,
            ], [
                'competition_id' => $cmId,
                'title' => $disciplines[0]->title,
                'short' => $disciplines[0]->short,
                'type' => $disciplines[0]->type,
                'id' => $disciplines[0]->id,
            ]]);

        $this->assertJsonCount($response, 3);
    }

    /**
     * A basic competition discipline request.
     * @return void
     */
    public function testCanGetCompetitionDiscipline()
    {
        $admin = $this->createSuperAdmin();
        $disciplines = $this->createDisciplines(3);
        $cmId = $disciplines[0]->competition_id;
        $discipline = $disciplines[1];

        $response = $this->get("/api/competitions/{$cmId}/disciplines/{$discipline->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'title' => $discipline->title,
                'short' => $discipline->short,
                'type' => $discipline->type,
                'game_type' => $discipline->game_type,
                'description' => $discipline->description,
                'id' => $discipline->id,
            ]);
    }

    /**
     * A basic competition discipline create request.
     * @return void
     */
    public function testCanCreateCompetitionDiscipline()
    {
        $admin = $this->createSuperAdmin();
        $competitions = factory(Competition::class, 3)->create();
        $competitionId = $competitions[1]->id;

        $url = "/api/competitions/{$competitionId}/disciplines";
        $response = $this->actingAs($admin, 'api')->postJson($url, [
            'competition_id' => $competitionId,
            'title' => 'title',
            'short' => 'short',
            'type' => Discipline::TYPE_KICKBOXING,
            'game_type' => 'game_type',
            'description' => 'description',
            'category_group_type' => Discipline::CAT_TYPE_AGE,
            'category_type' => Discipline::CAT_TYPE_WEIGHT,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $competitionId,
                'title' => 'title',
                'short' => 'short',
                'type' => Discipline::TYPE_KICKBOXING,
                'game_type' => 'game_type',
                'description' => 'description',
            ]);

        $this->assertDatabaseHas('disciplines', [
            'competition_id' => $competitionId,
            'title' => 'title',
        ]);
    }

    /**
     * A basic competition discipline update request.
     * @return void
     */
    public function testCanUpdateCompetitionDiscipline()
    {
        $admin = $this->createSuperAdmin();
        $competitions = factory(Competition::class, 3)->create();
        $competitionId = $competitions[1]->id;

        factory(Discipline::class)->create([
            'competition_id' => $competitionId,
        ]);

        $discipline = factory(Discipline::class)->create([
            'competition_id' => $competitionId,
        ]);

        $url = "/api/competitions/{$competitionId}/disciplines/{$discipline->id}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'id' => $discipline->id,
            'competition_id' => $competitionId,
            'title' => 'New Discipline Title',
            'short' => $discipline->short . ' New',
            'type' => Discipline::TYPE_KICKBOXING,
            'game_type' => $discipline->game_type . ' New',
            'description' => $discipline->description . ' New',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $discipline->id,
                'title' => 'New Discipline Title',
                'short' => $discipline->short . ' New',
                'type' => Discipline::TYPE_KICKBOXING,
                'game_type' => $discipline->game_type . ' New',
                'description' => $discipline->description . ' New',
            ]);

        $this->assertDatabaseHas('disciplines', [
            'id' => $discipline->id,
            'title' => 'New Discipline Title',
            'competition_id' => $competitionId,
        ]);
    }
}