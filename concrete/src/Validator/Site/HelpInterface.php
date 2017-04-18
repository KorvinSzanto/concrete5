<?php

namespace Concrete\Core\Validator\Site;

interface HelpInterface
{

    /**
     * Takes an error code matching possible errors returned from this validator
     *
     * @param int $code
     * @return string|null Help text to be displayed, can contain HTML
     */
    public function getHelpText($code);

}
