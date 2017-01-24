<?php
namespace Grid\Source;

use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\SourceInterface;

/**
 * Description of AbstractSource
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
abstract class AbstractSource implements SourceInterface, GridInterface
{
    use ExchangeArray, GridAwareTrait;
    
    /**
     * Query start from record
     * @var int
     */
    protected $offset = 0;

    /**
     * @var type
     */
    protected $limit = 0;

    /**
     * [columnName => ASC, DESC, columnName => ASC, DESC ...]
     * @var type
     */
    protected $order = [];

    /**
     * Result set count
     * @var type 
     */
    protected $count;

    /**
     * Result set
     * @var type
     */
    protected $rows;

    public function __construct(array $config)
    {
        $this->exchangeArray($config);
    }

    /**
     *
     * @return int
     */
    public function getOffset() : int
    {
        return $this->offset;
    }

    /**
     *
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     *
     * @return int
     */
    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     *
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function getOrder() : array
    {
        return $this->order;
    }

    /**
     *
     * @param array $order
     */
    public function setOrder(array $order)
    {
        foreach ($order as $name => &$direction) {
            if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                throw new Exception('order requires ASC or DESC');
            }
        }
        $this->order = $order;
    }
    
    /**
     *
     * @return array
     */
    public function getOrderFields() : array
    {
        $order = $this->getOrder();
        $orderFields = [];
        foreach ($order as $name => $direction) {
            $column = $this->getGrid()->getColumns()[$name];
            if (!$column->hasDbFields()) {
                trigger_error('Sortable column requires dbFields');
                continue;
            }
            if ($direction === 'ASC' || $direction === 'DESC') {
                foreach ($column->getDbFields() as $field) {
                    $orderFields[$this->getDbFieldNamespace($field)] = $direction;
                }
            }
        }

        return $orderFields;
    }
}
