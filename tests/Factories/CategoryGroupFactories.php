<?php namespace Tests\Factories;

use App\CategoryGroup;

/**
 * Trait CategoryGroupFactories
 * @package Tests\Factories
 */
trait CategoryGroupFactories
{
    protected function createGroups($count = 1)
    {
        $discipline1 = $this->createDiscipline();
        $discipline2 = $this->createDiscipline($discipline1->competition_id);
        $discipline3 = $this->createDiscipline($discipline1->competition_id);

        factory(CategoryGroup::class)->create([
            'competition_id' => $discipline1->competition_id,
            'discipline_id' => $discipline1->id,
        ]);

        $groups = factory(CategoryGroup::class)->times($count)->create([
            'competition_id' => $discipline2->competition_id,
            'discipline_id' => $discipline2->id,
        ]);

        factory(CategoryGroup::class)->create([
            'competition_id' => $discipline3->competition_id,
            'discipline_id' => $discipline3->id,
        ]);

        return $groups;
    }
}