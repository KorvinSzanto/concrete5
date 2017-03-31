<?php

namespace Concrete\Core\Search\Pagination\Adapter;

use Doctrine\DBAL\Query\QueryBuilder;

class InfiniteDbalAdapter implements InfiniteAdapter
{

    private $queryBuilder;
    private $countQueryBuilderModifier;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder              A DBAL query builder.
     * @param callable     $countQueryBuilderModifier A callable to modifier the query builder to count.
     */
    public function __construct(QueryBuilder $queryBuilder, $countQueryBuilderModifier)
    {
        if ($queryBuilder->getType() !== QueryBuilder::SELECT) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        if (!is_callable($countQueryBuilderModifier)) {
            throw new InvalidArgumentException('The count query builder modifier must be a callable.');
        }

        $this->queryBuilder = clone $queryBuilder;
        $this->countQueryBuilderModifier = $countQueryBuilderModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $qb = $this->prepareCountQueryBuilder();
        $result = $qb->execute()->fetchColumn();

        return (int) $result;
    }

    private function prepareCountQueryBuilder()
    {
        $qb = clone $this->queryBuilder;
        call_user_func($this->countQueryBuilderModifier, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $qb = clone $this->queryBuilder;
        if ($length > 0) {
            $qb->setMaxResults($length);
        }

        return $qb->setFirstResult($offset)->execute();
    }

    /**
     * Get results after a result
     * @param mixed|callable $after If a callable is passed, it must match function(mixed $mixed): bool;
     * @param $limit The amount to get. Pass ::ALL to get all results
     * @return \Iterator
     */
    public function getResultsAfter($after, $limit)
    {
        $qb = clone $this->queryBuilder;
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->execute();
    }

}
