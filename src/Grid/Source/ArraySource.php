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