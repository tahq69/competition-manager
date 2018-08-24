<?php namespace Tests\Feature;

use App\CategoryGroup;
use App\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CategoryGroupTest
 *
 * @package Tests\Feature
 */
class CategoryGroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition discipline groups list request.
     *
     * @return void
     */
    function testCanGetGroupsList()
    {
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
     * A basic competition discipline groups list with categories request.
     *
     * @group CategoryGroup
     * @return void
     */
    function testCanGetGroupsListWithCategories()
    {
        $groups = $this->createGroups(2);

        collect($groups)->each(function ($group) {
            $this->createCategories(2, $group->id);
        });

        $cmId = $groups[0]->competition_id;
        $disciplineId = $groups[0]->discipline_id;

        $uri = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/categories";
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
                'categories' => [
                    [
                        'competition_id' => $cmId,
                        'discipline_id' => $disciplineId,
                        'category_group_id' => $groups[0]->id,
                        'title' => $groups[0]->categories[0]->title,
                        'id' => $groups[0]->categories[0]->id,
                    ],
                    [
                        'competition_id' => $cmId,
                        'discipline_id' => $disciplineId,
                        'category_group_id' => $groups[0]->id,
                        'title' => $groups[0]->categories[1]->title,
                        'id' => $groups[0]->categories[1]->id,
                    ],
                ],
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'title' => $groups[1]->title,
                'short' => $groups[1]->short,
                'order' => $groups[1]->order,
                'id' => $groups[1]->id,
                'categories' => [
                    [
                        'competition_id' => $cmId,
                        'discipline_id' => $disciplineId,
                        'category_group_id' => $groups[1]->id,
                        'id' => $groups[1]->categories[0]->id,
                    ],
                    [
                        'competition_id' => $cmId,
                        'discipline_id' => $disciplineId,
                        'category_group_id' => $groups[1]->id,
                        'id' => $groups[1]->categories[1]->id,
                    ],
                ],
            ]]);

        $this->assertJsonCount($response, 2);
    }

    /**
     * A basic competition discipline group request.
     *
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
     *
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
                'type' => Discipline::CAT_TYPE_AGE,
            ]);

        $this->assertDatabaseHas('category_groups', [
            'competition_id' => $cmId,
            'title' => 'title',
        ]);
    }


    /**
     * A basic competition discipline group update request.
     *
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
                'type' => Discipline::CAT_TYPE_AGE,
            ]);

        $this->assertDatabaseHas('category_groups', [
            'competition_id' => $cmId,
            'title' => 'title-edited',
        ]);
    }
}