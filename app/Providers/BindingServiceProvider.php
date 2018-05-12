<?php namespace App\Providers;

use App\Contracts\IRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class BindingServiceProvider
 *
 * @package App\Providers
 */
class BindingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*$this->app->bind(\App\Contracts\IMessageRepository::class, \App\Repositories\MessageRepository::class);
        $this->app->bind(\App\Contracts\IPostRepository::class, \App\Repositories\PostRepository::class);*/
        $this->app->bind(\App\Contracts\IAreaRepository::class, \App\Repositories\AreaRepository::class);
        $this->app->bind(\App\Contracts\ICategoryGroupRepository::class, \App\Repositories\CategoryGroupRepository::class);
        $this->app->bind(\App\Contracts\ICategoryRepository::class, \App\Repositories\CategoryRepository::class);
        $this->app->bind(\App\Contracts\ICompetitionRepository::class, \App\Repositories\CompetitionRepository::class);
        $this->app->bind(\App\Contracts\IDisciplineRepository::class, \App\Repositories\DisciplineRepository::class);
        $this->app->bind(\App\Contracts\ITeamMemberRepository::class, \App\Repositories\TeamMemberRepository::class);
        $this->app->bind(\App\Contracts\ITeamRepository::class, \App\Repositories\TeamRepository::class);
        $this->app->bind(\App\Contracts\IUserRepository::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Contracts\IRoleRepository::class, \App\Repositories\RoleRepository::class);
    }

    /**
     * Resolve repository instance from table name or of its partial value.
     *
     * @param string $tableName Table full or partial name.
     *
     * @return \App\Contracts\IRepository
     * @throws \Exception
     */
    public static function resolveRepository(string $tableName): IRepository
    {
        $all = static::getRepositories();

        $repos = array_filter($all, function ($interface) use ($tableName) {
            /** @var \App\Contracts\IRepository $instance */
            $instance = app($interface);
            return strpos($instance->getTable(), str_plural($tableName)) !== false;
        });

        if (count($repos) !== 1) throw new \Exception(
            'Found invalid count of repositories for request parameter. ' .
            'Please check parameter binding namings to avoid overlaps.'
        );

        return app($repos[array_keys($repos)[0]]);
    }

    /**
     * Get listing of registered repository bindings.
     *
     * @return array
     */
    public static function getRepositories()
    {
        $abstracts = array_keys(app()->getBindings());

        return array_filter($abstracts, function ($interface) {
            if (strpos($interface, 'App\\Contracts') === false) return false;

            return in_array(IRepository::class, class_implements($interface));
        });
    }
}
