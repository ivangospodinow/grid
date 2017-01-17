<?php
namespace Grid\Source;

use Grid\Column\AbstractColumn;
use Grid\Interfaces\ColumnValuesInterface;

use \Exception;

/**
 * Description of AbstractSource
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ArraySource extends AbstractSource
{
    /**
     *
     * @var []
     */
    protected $driver = [];
    
    /**
     *
     * @param array $array
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['driver'])
        || !is_array($config['driver'])) {
            throw new Exception('ArraySource expects driver that contains array');
        }

        parent::__construct($config);
    }

    /**
     *
     * @return array
     */
    public function getRows()
    {
        $this->order();
        if ($this->getOffset() || $this->getLimit()) {
            return array_slice(
                $this->driver,
                $this->getOffset(),
                $this->getLimit()
            );
        }
        return $this->driver;
    }

    /**
     *
     * @param array $rows
     */
    public function setRows(array $rows)
    {
        $this->driver = $rows;
    }

    /**
     * $order = [column => direction]
     */
    public function order()
    {
        $order = $this->getOrder();
        foreach ($order as $name => $direction) {
            uasort($this->driver, function ($a, $b) use ($name, $direction) {
                if ($direction === 'ASC') {
                    return $a[$name] <=> $b[$name];
                } else {
                    return $b[$name] <=> $a[$name];
                }
            });
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
        $value = strtolower($value);
        $name  = $column->getName();

        foreach ($this->driver as $key => $row) {
            if (strtolower($row[$name]) != $value) {
                unset($this->driver[$key]);
            }
        }
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function andLike(AbstractColumn $column, string $value)
    {
        $value = strtolower($value);
        $name  = $column->getName();
        
        foreach ($this->driver as $key => $row) {
            if (strpos(strtolower($row[$name]), $value) === false) {
                unset($this->driver[$key]);
            }
        }
    }

    /**
     *
     * @param AbstractColumn $column
     * @return array
     */
    public function getColumnValues(AbstractColumn $column) : array
    {
        $dbFields = $column->getDbFields();
        $dbField = $dbFields[key($dbFields)];
        $values = [];
        foreach ($this->driver as $pair) {
            if (isset($pair[$dbField])) {
                $values[$pair[$dbField]] = $pair[$dbField];
            }
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
            $this->setCount(count($this->driver));
        }
        return $this->count;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }
}