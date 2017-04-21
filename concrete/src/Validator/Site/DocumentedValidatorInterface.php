<?php

namespace Concrete\Core\Validator\Site;

interface DocumentedValidatorInterface
{

    /**
     * Takes an error code matching possible errors returned from this validator
     *
     * @param int $code
     * @return string|null The url link to the documentation for resolving issues with this validator
     */
    public function linkForError($code);

    /**
     * Takes an error code matching possible errors returned from this validator
     *
     * @param int $code
     * @return string|null Help text to be displayed, can contain HTML
     */
    public function getHelpText($code);

}
