<?php

namespace Concrete\Core\Translation;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Translation\Repository\TranslationRepositoryManager;

class ConcreteTranslationServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        /**
         * @type TranslationRepositoryManager $manager
         */
        $manager = $this->app->make('concrete/translation/repository/manager');

        // Register config repositories
        $config_repositories = $this->app['config']['app.translation.repositories'];
        foreach ($config_repositories as $handle => $mixed) {
            $repository = $this->app->make($mixed);
            $manager->addRepository($handle, $repository);
        }
    }

}
