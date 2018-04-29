<?php namespace Tests\Factories;

use App\Competition;
use App\Team;

/**
 * Trait CompetitionFactories
 *
 * @package Tests\Factories
 */
trait CompetitionFactories
{
    protected function createCompetition(int $teamId = 0): Competition
    {
        $date = function (int $addDays, int $addHours = 12) {
            return \Carbon\Carbon::now()
                ->addDays($addDays)
                ->addHours($addHours)
                ->toDateTimeString();
        };

        $team = $this->getCmTeam($teamId);
        $competitions = factory(Competition::class)->times(3)->create([
            'team_id' => $team->id,
            'team_name' => $team->name,
            'team_short' => $team->short,
            'registration_till' => $date(2),
            'organization_date' => $date(3),
        ]);

        return $competitions[1];
    }

    private function getCmTeam(int $teamId): Team
    {
        if ($teamId == 0) {
            return factory(Team::class)->times(3)->create()[1];
        }

        return Team::find($teamId, ['id', 'name', 'short']);
    }
}
