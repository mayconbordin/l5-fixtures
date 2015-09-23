<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Support\ServiceProvider;
use Mayconbordin\L5Fixtures\Commands\DownCommand;
use Mayconbordin\L5Fixtures\Commands\UpCommand;

class FixturesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../../resources/config/config.php' => config_path('fixtures.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFixtures();
        $this->registerCommands();
        $this->mergeConfig();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerFixtures()
    {
        $this->app->bind('fixtures', function ($app) {
            return new Fixtures($app['config']->get('fixtures'));
        });

        $this->app->alias('fixtures', 'Mayconbordin\L5Fixtures\Fixtures');
    }

    /**
     * Register the application commands.
     */
    private function registerCommands()
    {
        $this->commands([
            UpCommand::class, DownCommand::class
        ]);
    }

    /**
     * Merges user's and entrust's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../resources/config/config.php', 'fixtures'
        );
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}