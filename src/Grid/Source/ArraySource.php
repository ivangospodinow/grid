<?php
namespace Grid\Source;

use \Exception;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
class ArraySource extends AbstractSource
{
    /**
     *
     * @var []
     */
    protected $driver;
    
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
        if ($this->getStart() || $this->getEnd()) {
            return array_slice(
                $this->driver,
                $this->getStart(),
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

    public function order()
    {
        if (empty($this->driver)) {
            return $this->driver;
        }

        $order = $this->getOrder();
        foreach ($order as $name => $direction) {
            if (!array_key_exists($name, $this->driver[key($this->driver)])) {
                error_log('Sorting ' . $name . ' does not exists in array');
                continue;
            }

            if ($direction === 'ASC' || $direction === 'DESC') {
                uasort($this->driver, function ($a, $b) use ($name, $direction) {
                    if ($direction === 'ASC') {
                        return $a[$name] <=> $b[$name];
                    } else {
                        return $b[$name] <=> $a[$name];
                    }
                });
            }
        }
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