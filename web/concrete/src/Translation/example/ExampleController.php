<?php

class ExampleController extends \Concrete\Core\Controller\Controller
{

    /**
     * Output the example view file and set the headers to javascipt type then send the response.
     */
    public function view()
    {
        $manager = $this->app->make('concrete/translation/repository/manager');

        $view = new View('views/translations.js');
        $view->addScopeItems(array('manager' => $manager));

        $response = new \Symfony\Component\HttpFoundation\Response($view->render());
        $response->headers->add(array('Content-Type' => 'application/javascript'));

        $response->send();
        $this->app->shutdown();
    }

}
