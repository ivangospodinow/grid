<?php
namespace Grid\Source;

use Grid\Util\Traits\ExchangeArray;

use \Exception;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
abstract class AbstractSource implements SourceInterface
{
    use ExchangeArray;
    
    /**
     * Query start from record
     * @var int
     */
    protected $start = 0;

    /**
     * Query ends to record
     * @var type
     */
    protected $end = 0;

    /**
     * $end - start
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
        $this->setLimit($this->getEnd() - $this->getStart());
    }
    
    /**
     *
     * @param int $start
     */
    public function setStart(int $start)
    {
        $this->start = $start;
    }

    /**
     *
     * @param int $end
     */
    public function setEnd(int $end)
    {
        $this->end = $end;
    }

    /**
     *
     * @return type
     */
    public function getStart() : int
    {
        return $this->start;
    }

    /**
     *
     * @return int
     */
    public function getEnd() : int
    {
        return $this->end;
    }

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
            $direction = strtoupper($direction);
            if (!in_array($direction, ['ASC', 'DESC'])) {
                unset($order[$name]);
                trigger_error('order requires ASC or DESC');
            }
        }
        $this->order = $order;
    }

        /**
     *
     * @return bool
     */
    public function canOrder() : bool
    {
        return count($this->order);
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
            $column = $this->getGrid()->getColumn($name);
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