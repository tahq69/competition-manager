<?php namespace Tests\Feature;

use App\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AreaTest
 * @package Tests\Feature
 */
class AreaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition area list request.
     * @return void
     */
    public function testCanGetAreaList()
    {
        factory(\App\User::class)->create();
        factory(\App\Area::class, 1);
        $competition = factory(\App\Competition::class)->create()->id;
        $areas = factory(\App\Area::class, 2)->create(['competition_id' => $competition]);
        factory(\App\Area::class, 1);

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
     * A basic competition area request.
     * @return void
     */
    public function testCanGetArea()
    {
        factory(\App\User::class)->create();
        factory(\App\Area::class, 1);
        /** @var Area $area */
        $area = factory(\App\Area::class)->create();
        factory(\App\Area::class, 1);

        $response = $this->get("/api/competitions/{$area->competition_id}/areas/{$area->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $area->competition_id,
                'type' => $area->type,
                'nr' => $area->nr,
                'title' => $area->title,
                'id' => $area->id,
            ]);
    }
}
