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
        $competitions = factory(Competition::class, 3)->create();
        $competitionId = $competitions[1]->id;
        factory(Discipline::class, 3)->create(['competition_id' => $competitions[0]->id]);
        factory(Discipline::class, 3)->create(['competition_id' => $competitions[2]->id]);
        $disciplines = factory(Discipline::class, 2)->create(['competition_id' => $competitions[1]->id]);

        $response = $this->get("/api/competitions/{$competitionId}/disciplines");

        $response
            ->assertStatus(200)
            ->assertJson([[
                'competition_id' => $disciplines[1]->competition_id,
                'title' => $disciplines[1]->title,
                'short' => $disciplines[1]->short,
                'type' => $disciplines[1]->type,
                'id' => $disciplines[1]->id,
            ], [
                'competition_id' => $disciplines[0]->competition_id,
                'title' => $disciplines[0]->title,
                'short' => $disciplines[0]->short,
                'type' => $disciplines[0]->type,
                'id' => $disciplines[0]->id,
            ]]);
    }

    /**
     * A basic competition discipline request.
     * @return void
     */
    public function testCanGetCompetitionDiscipline()
    {
        $admin = $this->createSuperAdmin();
        $competitions = factory(Competition::class, 3)->create();
        $competitionId = $competitions[1]->id;
        factory(Discipline::class, 3)->create(['competition_id' => $competitions[0]->id]);
        factory(Discipline::class, 3)->create(['competition_id' => $competitions[2]->id]);
        $disciplines = factory(Discipline::class, 3)->create(['competition_id' => $competitions[1]->id]);
        $discipline = $disciplines[1];
        $disciplineId = $discipline->id;

        $response = $this->get("/api/competitions/{$competitionId}/disciplines/{$disciplineId}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $discipline->competition_id,
                'title' => $discipline->title,
                'short' => $discipline->short,
                'type' => $discipline->type,
                'game_type' => $discipline->game_type,
                'description' => $discipline->description,
                'id' => $discipline->id,
            ]);
    }

    /**
     * A basic competition discipline update request.
     * @return void
     */
    public function testCanUpdateCompetitionDiscipline() {
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
            'competition_id' => $competitionId,
            'title' => 'New Discipline Title',
            'short' => $discipline->short . ' New',
            'type' => Discipline::KICKBOXING,
            'game_type' => $discipline->game_type . ' New',
            'description' => $discipline->description . ' New',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $discipline->id,
                'title' => 'New Discipline Title',
                'short' => $discipline->short. ' New',
                'type' => Discipline::KICKBOXING,
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