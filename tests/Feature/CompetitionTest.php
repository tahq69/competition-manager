<?php namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CompetitionTest
 * @package Tests\Feature
 */
class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition list request.
     * @return void
     */
    public function testCanGetCompetitionList()
    {
        factory(\App\User::class)->create();
        $competitions = factory(\App\Competition::class, 2)->create();

        $response = $this->get('/api/competitions');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 2,
                'data' => [[
                    'organization_date' => $competitions[1]->organization_date,
                    'judge_id' => $competitions[1]->judge_id,
                    'judge_name' => $competitions[1]->judge_name,
                    'title' => $competitions[1]->title,
                    'id' => $competitions[1]->id,
                ], [
                    'organization_date' => $competitions[0]->organization_date,
                    'judge_id' => $competitions[0]->judge_id,
                    'judge_name' => $competitions[0]->judge_name,
                    'title' => $competitions[0]->title,
                    'id' => $competitions[0]->id,
                ],],
            ]);
    }

    /**
     * Competition list request with ownership filtering.
     * @return void
     */
    public function testCanGetCompetitionListFilteredByOwnership()
    {
        factory(\App\User::class)->create();
        $manager = $this->createPostManager();
        $team = $this->createTeam([$manager]);


        // Create unowned competitions.
        factory(\App\Competition::class, 2)->create();

        $competitions = factory(\App\Competition::class, 2)->create(['team_id' => $team->id]);

        // And more unowned competitions.
        factory(\App\Competition::class, 2)->create();

        $response = $this
            ->actingAs($manager, 'api')
            ->get('/api/competitions?owned=true');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 2,
                'data' => [[
                    'id' => $competitions[1]->id,
                    'title' => $competitions[1]->title,
                ], [
                    'id' => $competitions[0]->id,
                    'title' => $competitions[0]->title,
                ],],
            ]);
    }

    /**
     * A basic competition list request.
     * @return void
     */
    public function testCanGetCompetition()
    {
        factory(\App\User::class)->create();
        $competitions = factory(\App\Competition::class, 3)->create();
        $comp = $competitions[1];

        $response = $this->get("/api/competitions/{$comp->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'organization_date' => $comp->organization_date,
                'registration_till' => $comp->registration_till,
                'judge_id' => $comp->judge_id,
                'judge_name' => $comp->judge_name,
                'price' => $comp->price,
                'equipment' => $comp->equipment,
                'prizes' => $comp->prizes,
                'ambulance' => $comp->ambulance,
                'rules' => $comp->rules,
                'program' => $comp->program,
                'invitation' => $comp->invitation,
                'cooperation' => $comp->cooperation,
                'subtitle' => $comp->subtitle,
                'title' => $comp->title,
                'id' => $comp->id,
            ]);
    }
}