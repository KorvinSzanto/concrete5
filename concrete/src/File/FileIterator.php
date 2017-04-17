<?php

namespace Concrete\Core\File;
;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Doctrine\DBAL\Query\QueryBuilder;
use Traversable;

class FileIterator implements \IteratorAggregate
{

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    private $connection;

    private $sort = [];

    /** @var \Doctrine\Common\Collections\Expr\Comparison[] */
    private $filter = [];

    private $parameters = [];

    private $preWrappers = [];
    private $wrappers = [];

    private $requiresVersion = false;
    private $requiresAttributes = [];

    private $offset = 0;

    private $length;

    private $streaming = false;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function includeVersion()
    {
        $this->requiresVersion = true;
    }

    public function includeAttribute(FileKey $key)
    {
        $this->requiresAttributes[$key->getAttributeKeyHandle()] = $key;
        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->prepareQuery();
    }

    /**
     * Get the query object that will output the list of files
     */
    protected function prepareQuery()
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('f.*')->from('Files', 'f');

        if ($this->requiresVersion) {
            $qb = $qb->addSelect('fv.*')->leftJoin('f', 'FileVersions', 'fv', 'f.fID = fv.fID');
        }

        $this->addAttributes($qb);

        foreach ($this->getSort() as $sort => $direction) {
            $qb = $qb->orderBy($sort, $direction);
        }

        if ($this->offset) {
            $qb = $qb->setFirstResult($this->offset);
        }

        if ($this->length && !$this->streaming) {
            $qb = $qb->setMaxResults($this->length);
        }

        foreach ($this->filter as $comparison) {
            $qb = $qb->andWhere($comparison);
        }

        $results = $qb->execute();

        if ($this->streaming || $this->wrappers || $this->preWrappers) {
            $results = $this->ensureIterator($results);
        }

        foreach ($this->preWrappers as $wrapper) {
            $results = $wrapper($results);
        }

        foreach ($this->wrappers as $wrapper) {
            $results = $wrapper($results);
        }

        if ($this->streaming && $this->length) {
            $limit = function (\Iterator $iterator) {
                $limit = $this->length;
                while ($limit-- && $iterator->valid()) {
                    yield $iterator->current();
                    $iterator->next();
                }
            };

            $results = $limit($results);
        }

        return $results;
    }

    public function all()
    {
        return iterator_to_array($this->getIterator());
    }

    protected function getSort()
    {
        if ($this->sort) {
            return $this->sort;
        }

        return ['f.fID' => 'asc'];
    }

    protected function sortByColumn($column, $direction = 'asc')
    {
        $this->sort[$column] = $direction;
        return $this;
    }

    public function sortByFileColumn($column, $direction = 'asc')
    {
        return $this->sortByColumn("f.{$column}", $direction);
    }

    public function sortByVersionColumn($column, $direction = 'asc')
    {
        $this->requiresVersion = true;
        return $this->sortByColumn("fv.{$column}", $direction);
    }

    public function sortByName($direction = 'asc')
    {
        return $this->sortByVersionColumn('fvFileName', $direction);
    }

    public function slice($offset, $length = null)
    {
        $this->offset = $offset;
        $this->length = $length;

        return $this;
    }

    /**
     * Starts yielding results after a certain item
     * @param $matcher
     * @return \Concrete\Core\File\FileIterator
     */
    public function startingWith($matcher)
    {
        $this->streaming = true;

        // If the matcher is a file ID, set the matcher to a new anonymous function
        if (is_numeric($matcher)) {
            $id = $matcher;
            $matcher = function ($item) use ($id) {
                $match = is_array($item) ? $item['fID'] : $item->getFileID();
                return $match == $id;
            };
        }

        // Wrap the iterator in a generator function that will only start outputting once the matcher evaluates to true
        $this->preWrappers[] = function (\Iterator $iterator) use ($matcher) {
            $outputting = false;
            foreach ($iterator as $value) {
                // If we're not outputting yet and the matcher evaluates to true
                if (!$outputting && $matcher($value)) {
                    $outputting = true;
                }

                // If we're still not outputting, continue
                if (!$outputting) {
                    continue;
                }

                // We're outputting now!
                yield $value;
            }
        };

        return $this;
    }

    /**
     * Wrap the result iterator with a generator
     * @param callable $wrapper
     * @return $this
     */
    public function addWrapper(callable $wrapper)
    {
        $this->wrappers[] = $wrapper;
        return $this;
    }

    /**
     * Get a page at an offset
     * @param $perPage
     * @param int $offset
     * @return \Traversable
     * @throws \InvalidArgumentException
     */
    public function page($perPage, $offset = 1)
    {
        if ($offset < 1) {
            throw new \InvalidArgumentException('Page must be at least 1');
        }
        $this->slice($perPage * ($offset - 1), $perPage);
        return $this->getIterator();
    }

    public function first()
    {
        return head($this->limit(1)->all());
    }

    public function limit($limit)
    {
        $this->length = $limit;
        return $this;
    }

    private function ensureIterator(\Traversable $results)
    {
        foreach ($results as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @param string|array $type The type or types to filter by
     * @return \Concrete\Core\File\FileIterator
     */
    public function filterByExtension($type)
    {
        $this->requiresVersion = true;
        $expr = $this->connection->getExpressionBuilder();
        if (is_array($type)) {
            $this->filter['type'] = $expr->in('fvExtension', array_map([$expr, 'literal'], $type));
        } else {
            $this->filter['type'] = $expr->comparison('fvExtension', '=', $expr->literal($type));
        }

        return $this;
    }

    protected function addAttributes(QueryBuilder $qb)
    {
        if ($this->requiresAttributes) {
            $qb->join('fv', 'FileAttributeValues', 'fav', 'fv.fvID=fav.fvID AND fv.fID = fav.fID');

            $qb->addSelect('FileAttributeKeys', 'fak');
            $qb->addSelect('FileAttributeValues', 'fak');

            /** @var FileKey $key */
            foreach ($this->requiresAttributes as $key) {
                $type = $key->getAttributeType();
                /** @var \Concrete\Core\Attribute\Controller $controller */
                $controller = $type->getController();
                $controller->table->getAttributeValueClass();

                $qb->addSelect($key->getAttributeType());
            }
        }
    }
}
