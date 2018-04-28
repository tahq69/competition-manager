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
        $discipline1 = $this->createDiscipline();
        $discipline2 = $this->createDiscipline($discipline1->competition_id);
        $discipline3 = $this->createDiscipline($discipline1->competition_id);
        $cmId = $discipline1->competition_id;

        $response = $this->get("/api/competitions/{$cmId}/disciplines");

        $response
            ->assertStatus(200)
            ->assertJson([[
                'competition_id' => $cmId,
                'title' => $discipline3->title,
                'short' => $discipline3->short,
                'type' => $discipline3->type,
                'id' => $discipline3->id,
            ], [
                'competition_id' => $cmId,
                'title' => $discipline2->title,
                'short' => $discipline2->short,
                'type' => $discipline2->type,
                'id' => $discipline2->id,
            ], [
                'competition_id' => $cmId,
                'title' => $discipline1->title,
                'short' => $discipline1->short,
                'type' => $discipline1->type,
                'id' => $discipline1->id,
            ]]);

        $this->assertJsonCount($response, 3);
    }

    /**
     * A basic competition discipline request.
     * @return void
     */
    public function testCanGetCompetitionDiscipline()
    {
        $this->createDiscipline();
        $discipline = $this->createDiscipline();
        $cmId = $discipline->competition_id;

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