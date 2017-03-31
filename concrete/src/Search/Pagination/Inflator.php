<?php

namespace Concrete\Core\Search\Pagination;

/**
 * Interface Inflator
 * @package Concrete\Core\Search\Pagination
 */
interface Inflator
{

    /**
     * Inflate a result before
     * @param mixed $result
     * @return mixed
     */
    public function inflate($result);

}
