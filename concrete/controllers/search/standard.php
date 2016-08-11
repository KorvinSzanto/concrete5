<?php
namespace Concrete\Controller\Search;

use Concrete\Controller\Dialog\Search\AdvancedSearch;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Result\Result;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Standard extends AbstractController
{

    abstract protected function canAccess();

    /**
     * @return AdvancedSearch
     */
    abstract protected function getAdvancedSearchDialogController();

    protected function getDefaultResetSearchResultObject()
    {
        return $this->getDefaultBasicSearchResultObject();
    }

    protected function getDefaultBasicSearchResultObject()
    {
        $advancedSearch = $this->getAdvancedSearchDialogController();
        $provider = $advancedSearch->getSearchProvider();
        $query = $advancedSearch->getSearchQuery();

        $query->setFields($this->getBasicSearchFieldsFromRequest());
        $query->setColumns($provider->getDefaultColumnSet());
        $result = $provider->getSearchResultFromQuery($query);
        $result->setBaseURL($advancedSearch->getBasicSearchBaseURL());

        return $result;
    }

    abstract protected function getSavedSearchPreset($presetID);

    protected function getBasicSearchFieldsFromRequest()
    {
        $advancedSearch = $this->getAdvancedSearchDialogController();
        $manager = $advancedSearch->getFieldManager();
        $fields = $manager->getFieldsFromRequest($this->request->query->all());
        return $fields;
    }

    public function searchBasic()
    {
        if ($this->canAccess()) {
            $result = $this->getDefaultBasicSearchResultObject();
            return new JsonResponse($result->getJSONObject());
        } else {
            return false;
        }
    }

    public function clearSearch()
    {
        if ($this->canAccess()) {
            $advancedSearch = $this->getAdvancedSearchDialogController();
            $provider = $advancedSearch->getSearchProvider();
            $provider->clearSessionCurrentQuery();

            $result = $this->getDefaultResetSearchResultObject();
            return new JsonResponse($result->getJSONObject());
        } else {
            return false;
        }
    }

    public function getCurrentSearchObject()
    {
        $advancedSearch = $this->getAdvancedSearchDialogController();
        $provider = $advancedSearch->getSearchProvider();
        $query = $provider->getSessionCurrentQuery();
        if (is_object($query)) {
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL($advancedSearch->getCurrentSearchBaseURL());
        } else {
            $result = $this->getDefaultBasicSearchResultObject();
            $result->setQuery(null); // This way the form element isn't dimmed on start.
        }
        return $result;
    }

    public function searchCurrent()
    {
        if ($this->canAccess()) {
            $result = $this->getCurrentSearchObject();
            return new JsonResponse($result->getJSONObject());
        }
    }

    protected function onAfterSearchPreset(Result $result, SavedSearch $search)
    {
        return $result;
    }

    public function searchPreset($presetID)
    {
        if ($this->canAccess()) {
            $preset = $this->getSavedSearchPreset($presetID);
            if (is_object($preset)) {
                $query = $preset->getQuery();
                if (is_object($query)) {
                    $advancedSearch = $this->getAdvancedSearchDialogController();
                    $provider = $advancedSearch->getSearchProvider();
                    $result = $provider->getSearchResultFromQuery($query);
                    $result->setBaseURL($advancedSearch->getSavedSearchBaseURL($preset));

                    $result = $this->onAfterSearchPreset($result, $preset);

                    return new JsonResponse($result->getJSONObject());
                }
            }
        }

    }



}
