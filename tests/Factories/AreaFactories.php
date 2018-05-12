<?php namespace Tests\Factories;

use App\Area;
use App\Competition;
use App\Team;

/**
 * Trait AreaFactories
 *
 * @package Tests\Factories
 */
trait AreaFactories
{
    protected function createArea(int $teamId = 0, int $cmId = 0): Area
    {
        $team = $this->getAreaTeam($teamId);
        $cm = $this->getAreaCompetition($cmId, $team->id);

        $competitions = factory(Area::class)->times(3)->create([
            'team_id' => $team->id,
            'competition_id' => $cm->id,
        ]);

        return $competitions[1];
    }

    private function getAreaCompetition(int $cmId, int $teamId): Competition
    {
        if ($cmId == 0) {
            $team = $this->getAreaTeam($teamId);
            $attr = ['team_id' => $team->id];
            return factory(Competition::class)->times(3)->create($attr)[1];
        }

        return Competition::find($cmId);
    }

    private function getAreaTeam(int $teamId): Team
    {
        if ($teamId == 0) {
            return factory(Team::class)->times(3)->create()[1];
        }

        return Team::find($teamId);
    }
}
