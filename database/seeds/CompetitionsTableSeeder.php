<?php

use App\Area;
use App\Category;
use App\CategoryGroup;
use App\Competition;
use App\Discipline;
use Illuminate\Database\Seeder;

/**
 * Class CompetitionsTableSeeder
 */
class CompetitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Competition::class, 10)->create()->each(function (Competition $cm) {
            $cm->disciplines()->saveMany(
                factory(Discipline::class, 2)->create([
                    'competition_id' => $cm->id,
                    'team_id' => $cm->team_id,
                ])->each(function (Discipline $discipline) use ($cm) {
                    // Lets create area for each discipline
                    $area = factory(Area::class)->create([
                        'competition_id' => $cm->id,
                        'team_id' => $cm->team_id,
                    ]);
                    factory(CategoryGroup::class, 4)->create([
                        'competition_id' => $cm->id,
                        'discipline_id' => $discipline->id,
                        'discipline_short' => $discipline->short,
                        'discipline_title' => $discipline->title,
                        'team_id' => $cm->team_id,
                    ])->each(function (CategoryGroup $group) use ($cm, $discipline, $area) {
                        factory(Category::class, 4)->create([
                            'area_id' => $area->id,
                            'category_group_id' => $group->id,
                            'category_group_short' => $group->short,
                            'category_group_title' => $group->title,
                            'competition_id' => $cm->id,
                            'discipline_id' => $discipline->id,
                            'discipline_short' => $discipline->short,
                            'discipline_title' => $discipline->title,
                            'team_id' => $cm->team_id,
                        ]);
                    });
                })
            );
        });
    }
}
