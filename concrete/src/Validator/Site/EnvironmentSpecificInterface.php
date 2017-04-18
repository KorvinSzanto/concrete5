<?php

namespace Concrete\Core\Validator\Site;

interface EnvironmentSpecificInterface
{

    const PRODUCTION = 'production';
    const DEVELOPMENT = 'dev';

    /**
     * Get the environment this is meant for
     * @return string [EnvironmentSpecificInterface::PRODUCTION || EnvironmentSpecificInterface::DEVELOPMENT]
     */
    public function getEnvironment();

}
