<?php

namespace Concrete\Core\Search\Pagination\Adapter;

class FilteredAdapter implements InfiniteAdapter
{

    protected $shouldCount;
    protected $filter;
    protected $adapter;

    /**
     * FilteredAdapter constructor.
     * @param \Concrete\Core\Search\Pagination\Adapter\InfiniteAdapter $adapter
     * @param callable $filter
     * @param bool $shouldCount
     */
    public function __construct(InfiniteAdapter $adapter, callable $filter, $shouldCount = false)
    {
        $this->adapter = $adapter;
        $this->shouldCount = $shouldCount;
        $this->filter = $filter;
    }

    /**
     * Set whether to count results
     *
     * @param $shouldCount
     * @return $this
     */
    public function setCountResults($shouldCount)
    {
        $this->shouldCount = !!$shouldCount;
        return $this;
    }

    /**
     * Set the filter to run the results through
     *
     * @param callable $filter
     * @return $this
     */
    public function setFilter(callable $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Get the number of results
     * @return int
     */
    public function getNbResults()
    {
        if ($this->shouldCount) {
            $results = $this->getResultsAfter(null, self::LIMIT_ALL);
            $count = 0;
            while ($results->valid()) {
                $count++;
                $results->next();
            }

            return $count;
        }

        return self::COUNT_UNKNOWN;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $results = $this->adapter->getSlice($offset, self::LIMIT_ALL);
        $filter = $this->filter;
        $count = 0;

        foreach ($results as $result) {
            if ($filter($result)) {
                yield $result;

                if ($length > 0) {
                    $count++;
                    if ($count > $length) {
                        return;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsAfter($after, $limit)
    {
        $results = $this->adapter->getResultsAfter($after, self::LIMIT_ALL);
        $filter = $this->filter;

        foreach ($results as $result) {
            if ($filter($result))  {
                yield $result;
            }
        }
    }

}
