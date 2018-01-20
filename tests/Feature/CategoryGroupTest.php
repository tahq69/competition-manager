<?php namespace Tests\Feature;

use App\CategoryGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CategoryGroupTest
 * @package Tests\Feature
 */
class CategoryGroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition discipline groups list request.
     * @return void
     */
    function testCanGetGroupsList()
    {
        $admin = $this->createSuperAdmin();
        $groups = $this->createGroups(3);
        $cmId = $groups[0]->competition_id;
        $disciplineId = $groups[0]->discipline_id;

        $uri = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups";
        $response = $this->get($uri);

        $response
            ->assertStatus(200)
            ->assertJson([[
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[0]->title,
                'short' => $groups[0]->short,
                'order' => $groups[0]->order,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[1]->title,
                'short' => $groups[1]->short,
                'order' => $groups[1]->order,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[2]->title,
                'short' => $groups[2]->short,
                'order' => $groups[2]->order,
            ],]);

        $this->assertJsonCount($response, 3);
    }

    /**
     * A basic competition discipline group request.
     * @return void
     */
    public function testCanGetGroupEntry()
    {
        $admin = $this->createSuperAdmin();
        $groups = $this->createGroups(3);
        $cmId = $groups[0]->competition_id;
        $disciplineId = $groups[0]->discipline_id;
        $group = $groups[1];
        $groupId = $group->id;

        $uri = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups/{$groupId}";
        $response = $this->get($uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $group->title,
                'short' => $group->short,
                'order' => $group->order,
            ]);
    }

    private function createGroups($count = 1)
    {
        $disciplines = $this->createDisciplines(3);
        // Create group for extra discipline at the beginning.
        factory(CategoryGroup::class)->create([
            'competition_id' => $disciplines[0]->competition_id - 1,
            'discipline_id' => $disciplines[0]->id - 1,
        ]);

        $result = [];

        collect($disciplines)->each(function ($discipline, $key) use (&$result, $count) {
            // Crate groups for all 3 disciplines.
            $groups = factory(CategoryGroup::class, $count)->create([
                'competition_id' => $discipline->competition_id,
                'discipline_id' => $discipline->id,
            ]);

            // But return only second discipline groups.
            if ($key == 1) {
                $result = $groups;
            }
        });

        // Create group for extra discipline at the end.
        factory(CategoryGroup::class)->create([
            'competition_id' => $disciplines[2]->competition_id + 1,
            'discipline_id' => $disciplines[2]->id + 1,
        ]);

        return $result;
    }
}