<?php namespace Tests\Feature;

use App\Area;
use App\Category;
use App\CategoryGroup;
use App\Discipline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CategoryTests
 *
 * @package Tests\Feature
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition discipline group categories list request.
     *
     * @return void
     */
    public function testCanGetCategoryList()
    {
        $admin = $this->createSuperAdmin();
        $categories = $this->createCategories(3);

        $cmId = $categories[0]->competition_id;
        $disciplineId = $categories[0]->discipline_id;
        $groupId = $categories[0]->category_group_id;

        $uri = "/api/competitions/{$cmId}/disciplines/{$disciplineId}" .
            "/groups/{$groupId}/categories";

        $response = $this->get($uri);

        $response
            ->assertStatus(200)
            ->assertJson([[
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'title' => $categories[0]->title,
                'short' => $categories[0]->short,
                'order' => $categories[0]->order,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'title' => $categories[1]->title,
                'short' => $categories[1]->short,
                'order' => $categories[1]->order,
            ], [
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'title' => $categories[2]->title,
                'short' => $categories[2]->short,
                'order' => $categories[2]->order,
            ],]);

        $this->assertJsonCount($response, 3);
    }

    /**
     * A basic competition discipline group category request.
     *
     * @return void
     */
    public function testCanGetCategory()
    {
        $admin = $this->createSuperAdmin();
        $categories = $this->createCategories(3);
        $category = $categories[1];

        $cmId = $category->competition_id;
        $disciplineId = $category->discipline_id;
        $groupId = $category->category_group_id;

        $uri = "/api/competitions/{$cmId}/disciplines/{$disciplineId}" .
            "/groups/{$groupId}/categories/{$category->id}";

        $response = $this->get($uri);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'title' => $category->title,
                'short' => $category->short,
                'order' => $category->order,
                'id' => $category->id,
            ]);
    }

    /**
     * A basic competition discipline group category create request.
     *
     * @return void
     */
    public function testCanCreateCategory()
    {
        $admin = $this->createSuperAdmin();
        $categories = $this->createCategories();
        /** @var Category $category */
        $category = $categories[0];

        $cmId = $category->competition_id;
        $disciplineId = $category->discipline_id;
        $groupId = $category->category_group_id;

        $area = factory(Area::class)->create(['competition_id' => $cmId]);
        $areaId = $area->id;

        $disciplineTitle = $category->discipline_title;
        $groupTitle = $category->category_group_title;

        $url = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups/{$groupId}/categories";
        $response = $this->actingAs($admin, 'api')->postJson($url, [
            'competition_id' => $cmId,
            'discipline_id' => $disciplineId,
            'category_group_id' => $groupId,
            'area_id' => $areaId,
            'title' => 'title',
            'short' => 'short',
            'display_type' => Category::DISPLAY_MAX,
            'min' => '3',
            'max' => '4',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'area_id' => $areaId,
                'title' => 'title',
                'short' => 'short',
                'min' => 3,
                'max' => 4,
                'display_type' => Category::DISPLAY_MAX,
                'type' => Discipline::CAT_TYPE_WEIGHT,
                'order' => 2,
                'discipline_title' => $disciplineTitle,
                'category_group_title' => $groupTitle,
            ]);

        $this->assertDatabaseHas('categories', [
            'competition_id' => $cmId,
            'title' => 'title',
        ]);
    }

    /**
     * A basic competition discipline group category update request.
     *
     * @return void
     */
    public function testCanUpdateGroupCategory()
    {
        $admin = $this->createSuperAdmin();
        /** @var Category $category */
        $category = $this->createCategories(3)[1];

        $cmId = $category->competition_id;
        $disciplineId = $category->discipline_id;
        $groupId = $category->category_group_id;
        $catId = $category->id;

        $area = factory(Area::class)->create(['competition_id' => $cmId]);
        $areaId = $area->id;

        $url = "/api/competitions/{$cmId}/disciplines/{$disciplineId}/groups/{$groupId}/categories/{$catId}";
        $response = $this->actingAs($admin, 'api')->patchJson($url, [
            'id' => $catId,
            'competition_id' => $cmId,
            'discipline_id' => $disciplineId,
            'category_group_id' => $groupId,
            'area_id' => $areaId,
            'title' => 'title-edited',
            'short' => 'short-edited',
            'display_type' => Category::DISPLAY_MIN,
            'min' => '5',
            'max' => '6',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'competition_id' => $cmId,
                'discipline_id' => $disciplineId,
                'category_group_id' => $groupId,
                'area_id' => $areaId,
                'title' => 'title-edited',
                'short' => 'short-edited',
                'display_type' => Category::DISPLAY_MIN,
                'min' => 5,
                'max' => 6,
            ]);

        $this->assertDatabaseHas('categories', [
            'competition_id' => $cmId,
            'title' => 'title-edited',
        ]);
    }
}
