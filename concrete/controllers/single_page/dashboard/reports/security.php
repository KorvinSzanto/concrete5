<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use Concrete\Core\Validator\Site\SiteValidatorManager;

class Security extends DashboardPageController
{

    /**
     * @var \Concrete\Core\Validator\Site\SiteValidatorManager
     */
    private $manager;
    /**
     * @var \Concrete\Core\Site\Service
     */
    private $siteService;

    public function __construct(Page $page, Service $siteService, SiteValidatorManager $manager)
    {
        parent::__construct($page);
        $this->manager = $manager;
        $this->siteService = $siteService;
    }

    public function view()
    {
        $result = new \ArrayIterator();
        $this->manager->setEnvironment($this->app->environment());
        $this->manager->isValid($this->siteService->getSite(), $result);

        $this->set('result', $this->splitByValidator($result));
    }

    /**
     * Splits an array of errors into smaller arrays keyed by the validator name
     * @param $errors
     * @return array
     */
    private function splitByValidator($errors)
    {
        $validators = iterator_to_array($this->manager->getValidators());
        $split = array_combine(array_keys($validators), array_pad([], count($validators), []));

        foreach ($errors as $error) {
            $key = array_search($error->getValidator(), $validators);
            $split[$key][] = $error;
        }

        return $split;
    }

}
