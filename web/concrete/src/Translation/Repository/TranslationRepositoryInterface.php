<?php
namespace Concrete\Core\Translation\Repository;

interface TranslationRepositoryInterface
{

    /**
     * Add a string that is translatable with some additional context.
     * $repository->addString('Translate this', '"this" refers to something being translated');
     *
     * @param $string         The string that is translatatble
     * @param string $context The context for the translatable string
     * @return void
     */
    public function addString($string, $context = "");

    /**
     * Get a list of strings that were added to this repository
     * $repository->addString('some string', 'some context');
     * $repository->addString('some other string');
     *
     * // $strings == array(array('some string', 'some context'), array('some other string', ''));
     * $strings = $repository->getStrings();
     *
     * @return string[][]
     */
    public function getStrings();

}
