<?php

namespace Concrete\Core\Validator\Site;

interface EnvironmentSpecific
{

    const PRODUCTION = 'prod';
    const DEVELOPMENT = 'dev';

    /**
     * Get the environment this is meant for
     * @return string [EnvironmentSpecific::PRODUCTION || EnvironmentSpecific::DEVELOPMENT]
     */
    public function getEnvironment();

}
