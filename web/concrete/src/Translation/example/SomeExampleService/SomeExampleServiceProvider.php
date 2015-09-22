<?php

class SomeExampleServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        $repository = new \Concrete\Core\Translation\Repository\TranslationRepository();
        $this->app->bindShared('some/example/translations', $repository);

        $this->addTranslations($repository);
    }

    public function addTranslations(\Concrete\Core\Translation\Repository\TranslationRepository $repository)
    {
        $config = $this->app['config']->get('some_example::translation_strings');

        foreach ($config as $string => $context) {
            $repository->addString($string, $context);
        }
    }

}
