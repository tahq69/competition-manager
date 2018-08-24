<?php namespace Tests\Factories;

use App\Category;
use App\CategoryGroup;

/**
 * Trait CategoryFactories
 *
 * @package Tests\Factories
 */
trait CategoryFactories
{
    protected function createCategories($count = 1, $group_id = 0)
    {
        $groups = [];
        $discipline = $this->createDiscipline();

        if ($group_id != 0) {
            $groups[0] = CategoryGroup::find($group_id);
            $discipline = $groups[0]->discipline;
        }

        if ($group_id == 0) {
            $groups = factory(CategoryGroup::class, 3)->create([
                'competition_id' => $discipline->competition_id,
                'discipline_id' => $discipline->id,
            ]);

            factory(Category::class)->create([
                'competition_id' => $discipline->competition_id,
                'discipline_id' => $discipline->id,
                'category_group_id' => $groups[0]->id,
            ]);
        }

        $result = factory(Category::class)->times($count)->create([
            'competition_id' => $discipline->competition_id,
            'discipline_id' => $discipline->id,
            'category_group_id' => $group_id == 0 ? $groups[1]->id : $group_id,
        ]);

        if ($group_id == 0) {
            factory(Category::class)->create([
                'competition_id' => $discipline->competition_id,
                'discipline_id' => $discipline->id,
                'category_group_id' => $groups[2]->id,
            ]);
        }

        return $result;
    }
}
