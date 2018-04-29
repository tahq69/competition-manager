<?php namespace Tests;

use App\Competition;
use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Discipline;
use App\Role;
use App\TeamMember;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,
        Factories\UserFactories,
        Factories\TeamFactories,
        Factories\TeamMemberFactories,
        Factories\CompetitionFactories,
        Factories\DisciplineFactories,
        Factories\CategoryFactories,
        Factories\CategoryGroupFactories;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed('RolesTableSeeder');
    }

    /**
     * @param  \Illuminate\Foundation\Testing\TestResponse $response
     * @param  int                                         $count
     *
     * @return void
     */
    protected function assertJsonCount($response, int $count): void
    {
        $this->assertTrue(
            count($response->json()) == $count,
            'Response record count is not equal to ' . $count
        );
    }
}
