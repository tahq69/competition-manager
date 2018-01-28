<?php namespace Tests\Feature;

use App\CategoryGroup;
use App\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
                'id' => $groups[0]->id,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[1]->title,
                'short' => $groups[1]->short,
                'order' => $groups[1]->order,
                'id' => $groups[1]->id,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[2]->title,
                'short' => $groups[2]->short,
                'order' => $groups[2]->order,
                'id' => $groups[2]->id,
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
                'id' => $group->id,
            ]);
    }

    /**
     * A basic competition discipline group create request.
     * @return void
     */
    public function testCanCreateGroup()
    {
        $admin = $this->createSuperAdmin();
        $groups = $this->createGroups();
        $cmId = $groups[0]->competition_id;
        $disciplineId = $groups[0]->discipline_id;
        $disciplineTitle = $groups[0]->discipline_title;

        $url = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups";
        $response = $this->actingAs($admin, 'api')->postJson($url, [
            'competition_id' => $cmId,
            'discipline_id' => $disciplineId,
            'title' => 'title',
            'short' => 'short',
            'rounds' => '1',
            'time' => '2',
            'min' => '3',
            'max' => '4',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => 'title',
                'short' => 'short',
                'rounds' => 1,
                'time' => 2,
                'min' => 3,
                'max' => 4,
                'order' => 2,
                'discipline_title' => $disciplineTitle,
                'type' => Discipline::TYPE_AGE,
            ]);

        $this->assertDatabaseHas('category_groups', [
            'competition_id' => $cmId,
            'title' => 'title',
        ]);
    }


    /**
     * A basic competition discipline group update request.
     * @return void
     */
    public function testCanUpdateGroup()
    {
        $admin = $this->createSuperAdmin();
        $group = $this->createGroups(3)[1];
        $groupId = $group->id;
        $cmId = $group->competition_id;
        $disciplineId = $group->discipline_id;
        $disciplineTitle = $group->discipline_title;

        $url = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups/{$groupId}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'id' => $groupId,
            'competition_id' => $cmId,
            'discipline_id' => $disciplineId,
            'title' => 'title-edited',
            'short' => 'short-edited',
            'rounds' => '1',
            'time' => '2',
            'min' => '3',
            'max' => '4',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => 'title-edited',
                'short' => 'short-edited',
                'rounds' => 1,
                'time' => 2,
                'min' => 3,
                'max' => 4,
                'discipline_title' => $disciplineTitle,
                'type' => Discipline::TYPE_AGE,
            ]);

        $this->assertDatabaseHas('category_groups', [
            'competition_id' => $cmId,
            'title' => 'title-edited',
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