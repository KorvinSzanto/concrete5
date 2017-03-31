<?php

namespace Concrete\Core\Search\Pagination\Adapter;

use Pagerfanta\Adapter\DoctrineDbalAdapter;

/**
 * Fancy adapter that allows sacrificing ->count accurracy
 * for the ability to filter the end result.
 *
 * This class may result in multiple queries where otherwise you may
 * only need one.
 */
class FilteredDbalAdapter extends DoctrineDbalAdapter
{

    protected $filter;
    protected $initialResult;

    /**
     * Set the filter to use
     *
     * @param callable $filter function(mixed $result):mixed
     */
    public function setFilter(callable $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        if (!$this->filter) {
            return parent::getSlice($offset, $length);
        } else {
            return $this->filterSlice($offset, $length);
        }
    }

    /**
     * Set the first result that should be processed
     * @param $result mixed|callable If a callable is passed, it will be called to determine the first item
     */
    public function setInitialResult($result)
    {
        $this->initialResult = $result;
    }

    /**
     * Get a slice of data starting at offset
     * @param $offset
     * @param $length
     * @return \Generator
     */
    protected function filterSlice($offset, $length)
    {
        $current = 0;
        $total = 0;

        while ($current < $length) {
            $needed = $this->totalNeeded($offset, $current);

            // Fetch some results
            $results = parent::filterSlice($offset + $total, $needed);

            // Add the count of items we just pulled
            $total += $needed;

            // Filter the results
            $filteredResults = $this->getFilteredResults($results);
            foreach ($filteredResults as $result) {
                yield $result;
                $current++;

                if ($current === $length) {
                    break;
                }
            }

            // We got less results than we asked for, we probably hit the end
            if ($filteredResults->getReturn() !== $needed) {
                break;
            }
        }
    }

    /**
     * Determine how many more to request
     *
     * @param $requested The amount we we're asked for
     * @param $current The current amount we have
     * @return mixed
     */
    protected function totalNeeded($requested, $current)
    {
        // Let's just try to get the default amount at first
        if (!$current) {
            return $requested;
        }

        // Find the difference and add 5
        return ($current - $requested) + 5;
    }

    /**
     * Filter results
     * @param array|\Traversable $results
     * @return \Generator
     */
    protected function getFilteredResults($results)
    {
        $tested = 0;
        $callable = $this->initialResult;
        $first = null === $callable;

        // Loop over results
        foreach ($results as $result) {
            if (!$first && $this->testInitialResult($result, $callable)) {
                $first = true;
            } else {
                continue;
            }

            $tested++;
            $filter = $this->filter;

            // filter the results
            if ($filterResult = $filter($result)) {
                yield $filterResult;
            }
        }

        return $tested;
    }

    /**
     * Determine if the two values match
     * @param $result The database row
     * @param $callable The value to test against or a callable to call
     * @return bool
     */
    protected function testInitialResult($result, $callable)
    {
        if (is_callable($callable)) {
            return $callable($result);
        } else {
            return $callable === $result;
        }
    }

}
