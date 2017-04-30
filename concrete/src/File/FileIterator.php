<?php
namespace Concrete\Core\File;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Tree\Node\Node;
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

    /** @var \Concrete\Core\Attribute\Category\FileCategory */
    private $category;

    private $parameters = [];

    private $preWrappers = [];
    private $wrappers = [];

    private $keywords = [];

    private $requiresVersion = false;
    private $requiresAttributes = false;
    private $requiresUser = false;
    private $requiresSets = false;
    private $requiresTree = false;

    private $offset = 0;

    private $length;

    private $streaming = false;

    public function __construct(Connection $connection, FileCategory $category = null)
    {
        $this->connection = $connection;
        $this->category = $category;
    }

    /**
     * @return $this
     */
    public function includeVersion()
    {
        $this->requiresVersion = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function includeAttributes()
    {
        $this->requiresAttributes = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function includeUser()
    {
        $this->requiresUser = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function includeSets()
    {
        $this->requiresSets = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function includeTree()
    {
        $this->requiresTree = true;
        return $this;
    }

    public function filterByKeyword($keyword)
    {
        $this->requiresVersion = true;
        $this->requiresAttributes = true;
        $this->requiresUser = true;

        $this->keywords[] = $keyword;

        return $this;
    }

    public function inFolder(Node $folder)
    {
        $this->requiresTree = true;

        $this->filter['folder'] = $this->connection->getExpressionBuilder()->eq('treeNodeParentID', $folder->getTreeNodeID());
        return $this;
    }

    public function filterByAttribute(FileKey $key, $value, $comparison = '=')
    {
        $handle = $key->getAttributeKeyHandle();
        $x = 'ak_' . $handle;
        $expr = $this->connection->getExpressionBuilder();
        $match = null;

        switch (strtolower($comparison)) {
            case '<>':
            case '!=':
                if ($value === null) {
                    $match = $expr->isNotNull($x);
                } else {
                    $match = $expr->neq($x, $expr->literal($value));
                }
                break;
            case '=':
            case '==':
                if ($value === null) {
                    $match = $expr->isNull($x);
                } else {
                    $match = $expr->eq($x, $expr->literal($value));
                }
                break;
            case '>=':
            case '=>';
                $match = $expr->gte($x, $expr->literal($value));
                break;
            case '<=':
            case '=<':
                $match = $expr->lte($x, $expr->literal($value));
                break;
            case '>':
                $match = $expr->gt($x, $expr->literal($value));
                break;
            case '<':
                $match = $expr->lt($x, $expr->literal($value));
                break;
            case 'like':
                $match = $expr->like($x, $expr->literal($value));
                break;
            case 'not like':
                $match = $expr->notLike($x, $expr->literal($value));
                break;
            case 'in':
                if (!is_array($value)) {
                    throw new \InvalidArgumentException(t('Invalid value provided for "in" comparison. Argument 2 must be an array.'));
                }
                $match = $expr->in($x, $value);
                break;
            case 'not in':
                if (!is_array($value)) {
                    throw new \InvalidArgumentException(t('Invalid value provided for "not in" comparison. Argument 2 must be an array.'));
                }
                $match = $expr->notIn($x, $value);
                break;
            default:
                throw new \InvalidArgumentException(t('Unsupported comparison "%s".', $comparison));
        }

        $this->requiresAttributes = true;
        $this->filter[] = $match;

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

    protected function joinIf($conditional, $join, $table, $alias, $pivot, $qb)
    {
        if ($conditional) {
            $qb = $qb->addSelect($alias . '.*')
                ->leftJoin($join, $table, $alias, $pivot);
        }

        return $qb;
    }

    /**
     * Get the query object that will output the list of files
     */
    protected function prepareQuery()
    {
        $qb = $this->connection->createQueryBuilder()->select('f.*')->from('Files', 'f');

        $qb = $this->applyJoins($qb);
        $qb = $this->applySort($qb);
        $qb = $this->applyOffset($qb);
        $qb = $this->applyLimit($qb);
        $qb = $this->applyFilter($qb);
        $qb = $this->applyKeywords($qb);

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

    public function sortByAttribute(FileKey $key, $direction = 'asc')
    {
        $this->requiresAttributes = true;
        return $this->sortByColumn('ak_' . $key->getAttributeKeyHandle(), $direction);
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

    public function filterBySet(Set $set = null)
    {
        $this->requiresSets = true;
        $expr = $this->connection->getExpressionBuilder();

        if ($set) {
            $this->filter['set'] = $expr->eq('fsf.fsID', $set->getFileSetID());
        } else {
            $this->filter['set'] = $expr->isNull('fsf.fsID');
        }

        return $this;
    }

    /**
     * Filter the list by extensions
     *
     * @param string|array $type The extension or extensions to filter by
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

    /**
     * Filter by a type integer
     * Maps to constants on \Concrete\Core\File\Type\Type
     *
     * @param int|array $type The type or types to filter by
     * @return \Concrete\Core\File\FileIterator
     */
    public function filterByType($type)
    {
        $this->requiresVersion = true;
        $expr = $this->connection->getExpressionBuilder();
        if (is_array($type)) {
            $this->filter['type'] = $expr->in('fvType', array_map([$expr, 'literal'], $type));
        } else {
            $this->filter['type'] = $expr->comparison('fvType', '=', $expr->literal($type));
        }

        return $this;
    }

    protected function applyKeywords(QueryBuilder $qb)
    {
        $expr = $qb->expr();
        $keys = [];

        if ($this->category) {
            $keys = $this->category->getSearchableIndexedList();
        }

        foreach ($this->keywords as $keyword) {
            $key = uniqid(':keyword_', false);
            $expressions = [
                $expr->like('fv.fvFilename', $key),
                $expr->like('fv.fvDescription', $key),
                $expr->like('fv.fvTitle', $key),
                $expr->like('fv.fvTags', $key),
                $expr->eq('u.uName', $key),
            ];

            foreach ($keys as $attributeKey) {
                $controller = $attributeKey->getController();
                $expressions[] = $controller->searchKeywords($keyword, $qb);
            }

            $qb->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
            $qb->setParameter($key, '%' . $keyword . '%');
        }

        return $qb;
    }

    /**
     * @param $qb
     * @return mixed
     */
    protected function applySort($qb)
    {
        foreach ($this->getSort() as $sort => $direction) {
            $qb = $qb->orderBy($sort, $direction);
        }
        return $qb;
    }

    /**
     * @param $qb
     * @return mixed
     */
    protected function applyOffset($qb)
    {
        if ($this->offset) {
            $qb = $qb->setFirstResult($this->offset);
        }
        return $qb;
    }

    /**
     * @param $qb
     * @return mixed
     */
    protected function applyLimit($qb)
    {
        if ($this->length && !$this->streaming) {
            $qb = $qb->setMaxResults($this->length);
        }
        return $qb;
    }

    /**
     * @param $qb
     * @return mixed
     */
    protected function applyFilter($qb)
    {
        foreach ($this->filter as $comparison) {
            $qb = $qb->andWhere($comparison);
        }
        return $qb;
    }

    /**
     * @param $qb
     * @return mixed
     */
    protected function applyJoins($qb)
    {
        $qb = $this->joinIf($this->requiresVersion, 'f', 'FileVersions', 'fv', 'f.fID = fv.fID', $qb);
        $qb = $this->joinIf($this->requiresUser, 'f', 'Users', 'u', 'f.uID = u.uID', $qb);
        $qb = $this->joinIf($this->requiresAttributes, 'f', 'FileSearchIndexAttributes', 'fsi', 'f.fID=fsi.fID', $qb);
        $qb = $this->joinIf($this->requiresSets, 'f', 'FileSetFiles', 'fsf', 'f.fID=fsf.fID', $qb);
        $qb = $this->joinIf($this->requiresTree, 'f', 'TreeFileNodes', 'tfn', 'f.fID=tfn.fID', $qb);
        $qb = $this->joinIf($this->requiresTree, 'tfn', 'TreeNodes', 'tn', 'tfn.treeNodeID=tn.treeNodeID', $qb);

        return $qb;
    }
}
