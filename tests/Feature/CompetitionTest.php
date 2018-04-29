<?php namespace Tests\Feature;

use App\Contracts\MemberRole;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CompetitionTest
 *
 * @package Tests\Feature
 */
class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic competition list request.
     *
     * @return void
     */
    public function testCanGetCompetitionList(): void
    {
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
     *
     * @return void
     */
    public function testCanGetCompetitionListFilteredByOwnership(): void
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
            ->get('/api/competitions?owned=1');

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
     * A basic test of create competition request.
     *
     * @return void
     */
    public function testCanCreateCompetition(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::CREATE_COMPETITIONS], 1);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/competitions', [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => Carbon::now()->addDays(2)->toDateTimeString(),
                'organization_date' => Carbon::now()->addDays(3)->toDateTimeString(),
                'team_id' => $team->id,
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'team_id' => $team->id,
                'team_name' => $team->name,
                'team_short' => $team->short,
            ]);

        $this->assertDatabaseHas('competitions', [
            'created_by' => $user->id,
            'title' => 'competition title',
        ]);
    }

    /**
     * Validate that team without credits cant create competition.
     *
     * @return void
     */
    public function testCantCreateCompetitionWithoutCredits(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::CREATE_COMPETITIONS], 0);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/competitions', [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => Carbon::now()->addDays(2)->toDateTimeString(),
                'organization_date' => Carbon::now()->addDays(3)->toDateTimeString(),
                'team_id' => $team->id,
            ]);

        $response
            ->assertStatus(422)
            ->assertSeeText('insufficient credit amount');
    }

    /**
     * Validate that ISO 8601 date formats are accepted by API on competition
     * create request.
     *
     * @return void
     */
    public function testCanCreateCompetitionWithISO8601Date(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::CREATE_COMPETITIONS], 1);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/competitions', [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => '2118-04-10T00:00:00.000Z',
                'organization_date' => '2118-05-10T00:00:00.000Z',
                'team_id' => $team->id,
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => '2118-04-10 00:00:00',
                'organization_date' => '2118-05-10 00:00:00',
            ]);;
    }

    /**
     * A basic competition list request.
     *
     * @return void
     */
    public function testCanGetCompetition(): void
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

    /**
     * A basic competition update request.
     *
     * @return void
     */
    public function testCanUpdateCompetition(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user]);
        $competition = $this->createCompetition($team->id);

        $regTill = Carbon::now()->addDays(2)->toDateTimeString();
        $orgDate = Carbon::now()->addDays(3)->toDateTimeString();

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/competitions/{$competition->id}", [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => $regTill,
                'organization_date' => $orgDate,
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => $regTill,
                'organization_date' => $orgDate,
            ]);

        $this->assertDatabaseHas('competitions', [
            'created_by' => $user->id,
            'title' => 'competition title',
        ]);
    }

    /**
     * Validate that ISO 8601 date formats are accepted by API on competition
     * update request.
     *
     * @return void
     */
    public function testCanUpdateCompetitionWithISO8601Date(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user]);
        $competition = $this->createCompetition($team->id);

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/competitions/{$competition->id}", [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => '2118-04-10T00:00:00.000Z',
                'organization_date' => '2118-05-10T00:00:00.000Z',
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => '2118-04-10 00:00:00',
                'organization_date' => '2118-05-10 00:00:00',
            ]);

        $this->assertDatabaseHas('competitions', [
            'created_by' => $user->id,
            'title' => 'competition title',
        ]);
    }

    /**
     * Validate that user without required role can`t update competition
     * details.
     *
     * @return void
     */
    public function simpleMemberCantUpdateCompetition(): void
    {
        $user = $this->createUser();
        $team = $this->createTeam([$user], [MemberRole::MANAGE_COMPETITION_AREAS]);
        $competition = $this->createCompetition($team->id);

        $regTill = Carbon::now()->addDays(2)->toDateTimeString();
        $orgDate = Carbon::now()->addDays(3)->toDateTimeString();

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/competitions/{$competition->id}", [
                'title' => 'competition title',
                'subtitle' => 'competition subtitle',
                'registration_till' => $regTill,
                'organization_date' => $orgDate,
            ]);

        $response->assertStatus(503);
    }
}
