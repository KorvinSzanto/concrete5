<?php

namespace Concrete\Core\Search\Pagination\Adapter;

use Pagerfanta\Adapter\AdapterInterface;

interface InfiniteAdapter extends AdapterInterface
{

    /** A constant for when you'd like no limit */
    const LIMIT_ALL = 0;

    /** A constant for when the length of the result set is unknown */
    const COUNT_UNKNOWN = -1;

    /**
     * Get results after a result
     * @param mixed|callable $after If a callable is passed, it must match function(mixed $mixed): bool;
     * @param $limit The amount to get. Pass ::ALL to get all results
     * @return \Iterator
     */
    public function getResultsAfter($after, $limit);

}
