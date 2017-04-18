<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Validator\ValidatorManager;

class SiteValidatorManager extends ValidatorManager
{

    protected $environment;

    /**
     * SiteValidatorManager constructor.
     */
    public function __construct()
    {
        $this->validators = [
            t('Canonical URL') => CanonicalUrlValidator::class,
            t('Session User Agent Matching') => SessionUserAgentMismatchValidator::class,
            t('Session IP Matching') => SessionIpMismatchValidator::class,
            t('X Frame Options') => XFrameOptionsValidator::class,
            t('Default Storage Location') => DefaultStorageLocationValidator::class,
            t('Caching') => CacheValidator::class
        ];
    }

    /**
     * Set the environment to validate against
     * Pass null to test all environments
     *
     * @param string|null $environment Ex: EnvironmentSpecificInterface::DEVELOPMENT
     */
    public function setEnvironment($environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * Get the validators bound to this object in a specific environment
     *
     * @return \Generator
     */
    public function getValidators()
    {
        $validators = parent::getValidators();

        foreach ($validators as $key => $validator) {
            // If it's not environment specific, just yield it
            if (!$validator instanceof EnvironmentSpecificInterface) {
                yield $key => $validator;
                continue;
            }

            // Otherwise, make sure it matches the environment
            if ($validator->getEnvironment() === $this->environment) {
                yield $key => $validator;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        $valid = true;
        foreach ($this->getValidators() as $name => $validator) {
            // Run the validation method
            if (!$validator->isValid($mixed, $error)) {
                $valid = false;
            }
        }

        return $valid;
    }
}
