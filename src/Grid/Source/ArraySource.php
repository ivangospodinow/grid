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
    protected $array = [];
    protected $count;
    
    /**
     *
     * @param array $array
     * @throws Exception
     */
    public function __construct(array $array)
    {
        if (!is_array($array[key($array)])) {
            throw new Exception('ArraySource expects array of arrays');
        }
        $this->array = $array;
    }

    /**
     *
     * @return array
     */
    public function getRows()
    {
        return $this->array;
    }

    /**
     *
     * @param array $rows
     */
    public function setRows(array $rows)
    {
        $this->array = $rows;
    }
    
    /**
     *
     * @return int
     */
    public function getCount() : int
    {
        if (null === $this->count) {
            $this->setCount(count($this->array));
        }
        return $this->count;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }
}