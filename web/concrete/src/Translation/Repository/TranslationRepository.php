<?php

namespace Concrete\Core\Translation\Repository;

class TranslationRepository implements TranslationRepositoryInterface
{

    /**
     * The array of strings
     * @type array
     */
    protected $strings = array();

    /**
     * {@inheritDoc}
     */
    public function addString($string, $context = "")
    {
        $this->strings[] = array($string, $context);
    }

    /**
     * @inheritDoc
     */
    public function getStrings()
    {
        return $this->strings;
    }

}
