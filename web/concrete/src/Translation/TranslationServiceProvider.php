<?php
namespace Concrete\Core\Translation;

use Concrete\Core\Translation\Repository\TranslationRepositoryManager;

class TranslationServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('concrete/translation/repository/manager', function() {
            $manager = new TranslationRepositoryManager();
            return $manager;
        });
    }

}
