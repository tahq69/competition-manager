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
        $admin = $this->createSuperAdmin();
        $competitions = factory(\App\Competition::class, 2)->create();

        $response = $this->get('/api/competitions');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 2,
                'data' => [[
                    'organization_date' => $competitions[0]->organization_date,
                    'registration_till' => $competitions[0]->registration_till,
                    'judge_id' => $competitions[0]->judge_id,
                    'judge_name' => $competitions[0]->judge_name,
                    'price' => $competitions[0]->price,
                    'equipment' => $competitions[0]->equipment,
                    'prizes' => $competitions[0]->prizes,
                    'ambulance' => $competitions[0]->ambulance,
                    'rules' => $competitions[0]->rules,
                    'program' => $competitions[0]->program,
                    'invitation' => $competitions[0]->invitation,
                    'cooperation' => $competitions[0]->cooperation,
                    'subtitle' => $competitions[0]->subtitle,
                    'title' => $competitions[0]->title,
                ], [
                    'organization_date' => $competitions[1]->organization_date,
                    'registration_till' => $competitions[1]->registration_till,
                    'judge_id' => $competitions[1]->judge_id,
                    'judge_name' => $competitions[1]->judge_name,
                    'price' => $competitions[1]->price,
                    'equipment' => $competitions[1]->equipment,
                    'prizes' => $competitions[1]->prizes,
                    'ambulance' => $competitions[1]->ambulance,
                    'rules' => $competitions[1]->rules,
                    'program' => $competitions[1]->program,
                    'invitation' => $competitions[1]->invitation,
                    'cooperation' => $competitions[1]->cooperation,
                    'subtitle' => $competitions[1]->subtitle,
                    'title' => $competitions[1]->title,
                ],],
            ]);
    }

    /**
     * Competition list request with ownership filtering.
     * @return void
     */
    public function testCanGetCompetitionListFilteredByOwnership()
    {
        $admin = $this->createSuperAdmin();
        $manager = $this->createPostManager();
        $adminDetails = [$manager->id => [
            'created_by_name' => $manager->name,
            'created_by' => $manager->id,
        ]];

        // Create unowned competitions.
        factory(\App\Competition::class, 2)->create();

        $competitions = factory(\App\Competition::class, 2)->create();
        // Assign ownership to competitions.
        $competitions[0]->managers()->sync($adminDetails);
        $competitions[1]->managers()->sync($adminDetails);

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
                    'id' => $competitions[0]->id,
                    'title' => $competitions[0]->title,
                ], [
                    'id' => $competitions[1]->id,
                    'title' => $competitions[1]->title,
                ],],
            ]);
    }
}