<?php
namespace Grid\Source;

use Grid\Source\Interfaces\QuerySourceInterface;
use Grid\Source\Traits\FilterGridQuery;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

use \Exception;

/**
 *
 * @author Gospodinow
 */
class ZendDbAdapterSource extends AbstractSource implements GridInterface, QuerySourceInterface
{
    use FilterGridQuery, GridAwareTrait;
    
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
    public function order()
    {
        $orderFields = $this->getOrderFields();
        if (!empty($orderFields)) {
            $this->getQuery()->order($orderFields);
        }
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
     * users.name
     * @param type $field
     * @return string
     */
    protected function getDbFieldNamespace($field) : string
    {
        return ($this->namespace ? $this->namespace . '.' : '') . $field;
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