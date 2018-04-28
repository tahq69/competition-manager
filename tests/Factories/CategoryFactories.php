<?php namespace Tests\Factories;

use App\Category;
use App\CategoryGroup;

/**
 * Trait CategoryFactories
 * @package Tests\Factories
 */
trait CategoryFactories
{
    protected function createCategories($count = 1)
    {
        $discipline = $this->createDiscipline();

        $groups = factory(CategoryGroup::class, 3)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
        ]);

        factory(Category::class)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
            'category_group_id' => $groups[0]->id,
        ]);

        $result = factory(Category::class)->times($count)->create([
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
