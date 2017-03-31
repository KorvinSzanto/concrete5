<?php

namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Legacy\DatabaseItemList;
use Concrete\Core\Search\ItemList\Database\ItemList as SearchDatabaseItemList;
use Concrete\Core\Search\ItemList\EntityItemList;

/**
 * A Pagination implementation that supports infinite lists
 */
class InfinitePagination extends Pagination
{

    protected $firstResult;

    public function setFirstResult($result)
    {
        $this->firstResult = $result;
        return $this;
    }

    /**
     * A generator implementation that gradually inflates items
     * @return \Generator
     */
    public function getCurrentPageResults()
    {
        $results = $this->getItemListResult();
        $total = $this->getMaxPerPage();

        // Inflate results
        foreach ($results as $result) {
            if ($r = $this->list->getResult($result)) {
                yield $r;
                if (!--$total) {
                    break;
                }
            }
        }
    }

    /**
     * Get the first result that should be outputted. 0 means start from the top
     * @return int|mixed|callable Returns the first result or a callable that can be used to determine the first result
     */
    protected function calculateOffsetForCurrentPageResults()
    {
        if (!$this->firstResult) {
            return 0;
        }

        return $this->firstResult;
    }

    protected function getItemListResult()
    {
        if ($this->list instanceof SearchDatabaseItemList || $this->list instanceof EntityItemList) {
            return $this->list->deliverQueryObject()->execute();
        } elseif ($this->list instanceof DatabaseItemList) {
            return $this->list->executeGetResults();
        }
    }

}
