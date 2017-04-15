<?php
namespace Concrete\Core\Validator;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\Site\SiteValidatorManager;
use Concrete\Core\Validator\SiteValidator;

class ValidatorServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        // Bind the manager interface to the default implementation
        $this->app->bind(
            '\Concrete\Core\Validator\ValidatorManagerInterface',
            '\Concrete\Core\Validator\ValidatorManager');

        $this->app->bind('validator/site', SiteValidatorManager::class);
    }
}
