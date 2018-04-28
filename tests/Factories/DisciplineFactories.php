<?php namespace Tests\Factories;

use App\Competition;
use App\Discipline;
use App\Team;

/**
 * Trait DisciplineFactories
 * @package Tests
 */
trait DisciplineFactories
{
    public function createDiscipline(
        int $cmId = 0,
        int $teamId = 0,
        array $attributes = []
    ): Discipline
    {
        $cm = $this->getCompetition($cmId, $teamId);
        $attr = array_merge(['competition_id' => $cm->id], $attributes);

        return factory(Discipline::class)->create($attr);
    }

    private function getCompetition(int $cmId, int $teamId): Competition
    {
        if ($cmId == 0) {
            $team = $this->getTeam($teamId);
            $attr = ['team_id' => $team->id];
            return factory(Competition::class)->times(3)->create($attr)[1];
        }

        return Competition::find($cmId);
    }

    private function getTeam(int $teamId): Team
    {
        if ($teamId == 0) {
            return factory(Team::class)->times(3)->create()[1];
        }

        return Team::find($teamId);
    }
}
