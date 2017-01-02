<?php
namespace Grid\Source;

use Grid\Source\Interfaces\QuerySourceInterface;
use Grid\Source\Traits\FilterGridQuery;
use Grid\Source\Traits\NamespaceAwareTrait;

use Grid\Column\AbstractColumn;
use Grid\Plugin\Interfaces\ColumnValuesInterface;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\Like;

use \Exception;

/**
 *
 * @author Gospodinow
 */
class ZendDbAdapterSource extends AbstractSource implements QuerySourceInterface
{
    use FilterGridQuery, NamespaceAwareTrait;
    
    /**
     *
     * @var string
     */
    protected $table;

    /**
     *
     * @var string
     */
    protected $namespace;
    
    /**
     * Primery key
     * @var type
     */
    protected $pk;

    /**
     *
     * @var Select
     */
    protected $query;
    
    /**
     *
     * @var QuerySourceInterface
     */
    protected $driver;
    
    /**
     *
     * @var Sql
     */
    protected $sql;

    /**
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!class_exists('Zend\Db\Sql\Sql')) {
            throw new Exception('Zend db is not installed. Run composer require zendframework/zend-db');
        }

        parent::__construct($config);
        
        if (!$this->driver instanceof AdapterInterface) {
            throw new Exception('driver must be instance of AdapterInterface');
        }

        if (!$this->table) {
            throw new Exception('table is required');
        }
    }

    /**
     *
     * @return array
     */
    public function getRows()
    {
        if (null === $this->rows) {
            $this->rows = $this->getSql()
                           ->prepareStatementForSqlObject($this->getQuery())
                           ->execute();
        }
        return $this->rows;
    }

    /**
     *
     * @param array $rows
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
    }
    
    /**
     * Added order to query
     */
    protected function order()
    {
        $orderFields = $this->getOrderFields();
        if (!empty($orderFields)) {
            $this->getQuery()->order($orderFields);
        }
    }
    
    /**
     *
     * @param AbstractColumn $column
     * @param string $sign
     * @param string $value
     */
    public function andWhere(AbstractColumn $column, string $sign, string $value)
    {
        $this->where($column, $sign, $value, PredicateSet::OP_AND);
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $sign
     * @param string $value
     */
    public function orWhere(AbstractColumn $column, string $sign, string $value)
    {
        $this->where($column, $sign, $value, PredicateSet::OP_OR);
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $sign
     * @param string $value
     * @param type $op
     */
    public function where(AbstractColumn $column, string $sign, string $value, $op = PredicateSet::OP_OR)
    {
        $set = new PredicateSet;
        foreach ($column->getDbFields() as $field) {
            $set->addPredicate(
                new Operator(
                    $this->getDbFieldNamespace($field),
                    $sign,
                    $value
                ),
                $op
            );
        }
        $this->getQuery()->where($set);
    }
    
    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function andLike(AbstractColumn $column, string $value)
    {
        $this->like($column, $value, PredicateSet::OP_AND);
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function orLike(AbstractColumn $column, string $value)
    {
        $this->like($column, $value, PredicateSet::OP_AND);
    }
    
    public function like(AbstractColumn $column, string $value, $op = PredicateSet::OP_OR)
    {
        $set = new PredicateSet;
        foreach ($column->getDbFields() as $field) {
            $set->addPredicate(
                new Like(
                    $this->getDbFieldNamespace($field),
                    "%$value%"
                ),
                $op
            );
        }
        $this->getQuery()->where($set);
    }
    
    /**
     * dbFields [name]                = [name => name]         WHERE name LIKE selectable
     * dbFields [id, name]            = [id => name]           WHERE id = selectable
     * dbFields [id, first, last ...] = [id => first last ...] WHERE id = selectable
     *
     * @param AbstractColumn $column
     * @return array
     */
    public function getColumnValues(AbstractColumn $column) : array
    {
        $query = clone $this->getQuery();
        $dbFields = $column->getDbFields();
        $first = key($dbFields);
        $idKey = $dbFields[$first];
        unset($dbFields[$first]);
        
        $columns = [];
        $columns['id'] = $this->getDbFieldNamespace($idKey);
        if (empty($dbFields)) {
            $columns['value'] = $this->getDbFieldNamespace($idKey);
        } elseif (count($dbFields) === 1) {
            $columns['value'] = $this->getDbFieldNamespace($dbFields[key($dbFields)]);
        } else {
            $concat = [];
            foreach ($dbFields as $dbField) {
                $concat[] = '`' . $this->getDbFieldNamespace($dbField) . '`';
            }
            $columns['value'] = new Expression("CONCAT_WS(' ', " . implode(',', $concat) . ")");
        }
        
        $query->columns($columns);
        $query->reset('group');
        $query->reset('order');
        $query->reset('limit');
        $query->reset('offset');
        
        $result = $this->getSql()
                       ->prepareStatementForSqlObject($query)
                       ->execute();

        $values = [];
        foreach ($result as $row) {
            $values[$row['id']] = $row['value'];
        }
        asort($values);

        return
        $this->getGrid()->filter(
            ColumnValuesInterface::class,
            'filterColumnValues',
            $values
        );
    }

    /**
     *
     * @return int
     */
    public function getCount() : int
    {
        if (null === $this->count) {
            $query = clone $this->getQuery();
            if ($this->pk) {
                $expr = 'COUNT(DISTINCT `' . $this->getDbFieldNamespace($this->pk) . '`)';
            } else {
                $expr = 'COUNT(*)';
            }
            $query->columns(['count' => new Expression($expr)]);

            $query->reset('group');
            $query->reset('order');
            $query->limit(1);
            $query->offset(0);
            
            $result = $this->getSql()->prepareStatementForSqlObject($query)->execute()->current();
            $this->setCount((int) ($result && isset($result['count']) ? $result['count'] : 0));
        }
        return $this->count;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }
    
    /**
     *
     * @return Select
     */
    public function getQuery()
    {
        if (null === $this->query) {
            $select = $this->getSql()->select($this->table);
            if ($this->getStart() || $this->getEnd()) {
                $select->limit($this->getEnd() - $this->getStart());
                $select->offset($this->getStart());
            }
            $this->setQuery(
                $this->filterGridQuery(
                    $this->getGrid(),
                    $select
                )
            );
        }
        
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     *
     * @return Sql
     */
    protected function getSql()
    {
        if (null === $this->sql) {
            $this->sql = new Sql($this->getAdapter());
        }
        return $this->sql;
    }

    /**
     *
     * @return AdapterInterface
     */
    protected function getAdapter() : AdapterInterface
    {
        return $this->driver;
    }
}