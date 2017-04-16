<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Validator\AbstractTranslatableValidator;
use Concrete\Core\Validator\ErrorLevel;

abstract class SiteValidator extends AbstractTranslatableValidator
{

    /**
     * Get a site error object
     *
     * @param string $string
     * @param string $severity
     * @param int $code
     * @return \Concrete\Core\Validator\Site\SiteError
     */
    protected function getError($string, $severity, $code)
    {
        return (new SiteError())->setMessage($string)->setSeverity($severity)->setCode($code)->setValidator($this);
    }

    /**
     * Add an error to the output array
     *
     * @param int $error
     * @param string $defaultString
     * @param string $severity
     * @param \ArrayIterator|null $array
     */
    protected function addError($error, $defaultString, $severity, $array)
    {
        if (null !== $array) {
            $array[] = $this->getError($this->getErrorString($error, null, $defaultString), $severity, $error);
        }
    }

    protected function addMustFix($error, $defaultString, $array)
    {
        $this->addError($error, $defaultString, ErrorLevel::MUSTFIX, $array);
    }

    protected function addRecommended($error, $defaultString, $array)
    {
        $this->addError($error, $defaultString, ErrorLevel::RECOMMENDATION, $array);
    }

    protected function add($error, $defaultString, $array)
    {
        $this->addError($error, $defaultString, ErrorLevel::RECOMMENDATION, $array);
    }

}
