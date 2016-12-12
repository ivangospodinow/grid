<?php
namespace Grid\Source;

use Grid\Source\Interfaces\QuerySourceInterface;
use Grid\Util\Traits\ExchangeArray;
use Grid\Source\Traits\FilterGridQuery;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Pdo as ZendPdo;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

use \PDO;
use \Exception;

/**
 * At this time pdo driver query will be string
 *
 * @author Gospodinow
 */
class MysqlPdoSource extends AbstractSource implements GridInterface, QuerySourceInterface
{
    use ExchangeArray, FilterGridQuery, GridAwareTrait;
    
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
     * @var PDO
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
        $this->exchangeArray($config);
        if (!$this->driver instanceof PDO) {
            throw new Exception('driver must be instance of PDO');
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
            $result = $this->getSql()
                           ->prepareStatementForSqlObject($this->getQuery())
                           ->execute();
            $this->rows = [];
            foreach ($result as $row) {
                $this->rows[] = $row;
            }
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
     *
     * @return int
     */
    public function getCount() : int
    {
        if (null === $this->count) {
            $query = clone $this->getQuery();
            if ($this->pk && $this->namespace) {
                $expr = 'COUNT(DISTINCT `' . $this->namespace . '`.`' . $this->pk . '`)';
            } elseif ($this->pk) {
                $expr = 'COUNT(DISTINCT `' . $this->pk . '`)';
            } else {
                $expr = 'COUNT(*)';
            }
            $query->columns(['count' => new Expression($expr)]);
            $query->reset('group');
            $query->reset('limit');
            $query->order('offset');
            $query->reset('order');
            $query->limit(1);
            
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
            if (null !== $this->getStart() && null !== $this->getEnd()) {
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
            $this->sql = new Sql(new Adapter(new ZendPdo($this->driver)));
        }
        return $this->sql;
    }
}