<?php namespace Tests\Feature;

use App\Category;
use App\CategoryGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CategoryTests
 * @package Tests\Feature
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition discipline group categories list request.
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

    private function createCategories($count)
    {
        $discipline = $this->createDisciplines()[0];
        $groups = factory(CategoryGroup::class, 3)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
        ]);

        factory(Category::class)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
            'category_group_id' => $groups[0]->id,
        ]);

        $result = factory(Category::class, $count)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
            'category_group_id' => $groups[1]->id,
        ]);

        factory(Category::class)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
            'category_group_id' => $groups[2]->id,
        ]);

        return $result;
    }
}