<?php namespace Tests\Unit;

use App\Contracts\IAreaRepository;
use App\Contracts\ICategoryGroupRepository;
use App\Contracts\ICategoryRepository;
use App\Contracts\ICompetitionRepository;
use App\Contracts\IDisciplineRepository;
use App\Contracts\IMessageRepository;
use App\Contracts\ITeamMemberRepository;
use App\Contracts\ITeamRepository;
use App\Providers\BindingServiceProvider;
use Tests\TestCase;

/**
 * Class BindingServiceTest
 *
 * @package Tests\Unit
 */
class BindingServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCanGetRepositoryBinding()
    {
        $repositories = BindingServiceProvider::getRepositories();

        $this->assertTrue(in_array(ICompetitionRepository::class, $repositories));
        $this->assertTrue(in_array(ICategoryRepository::class, $repositories));
        $this->assertTrue(in_array(ICategoryGroupRepository::class, $repositories));
        $this->assertTrue(in_array(IAreaRepository::class, $repositories));
        $this->assertTrue(in_array(IDisciplineRepository::class, $repositories));
        $this->assertTrue(in_array(ITeamMemberRepository::class, $repositories));
        $this->assertTrue(in_array(IMessageRepository::class, $repositories));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testCanResolveRepositoryBinding()
    {
        $this->assertInstanceOf(
            ICompetitionRepository::class,
            BindingServiceProvider::resolveRepository('competition')
        );

        $this->assertInstanceOf(
            ICategoryRepository::class,
            BindingServiceProvider::resolveRepository('category')
        );

        $this->assertInstanceOf(
            ICategoryGroupRepository::class,
            BindingServiceProvider::resolveRepository('group')
        );

        $this->assertInstanceOf(
            IAreaRepository::class,
            BindingServiceProvider::resolveRepository('area')
        );

        $this->assertInstanceOf(
            IDisciplineRepository::class,
            BindingServiceProvider::resolveRepository('discipline')
        );

        $this->assertInstanceOf(
            ITeamRepository::class,
            BindingServiceProvider::resolveRepository('team')
        );

        $this->assertInstanceOf(
            ITeamMemberRepository::class,
            BindingServiceProvider::resolveRepository('member')
        );

        $this->assertInstanceOf(
            IMessageRepository::class,
            BindingServiceProvider::resolveRepository('message')
        );
    }
}
