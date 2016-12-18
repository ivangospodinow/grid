<?php
namespace Grid\DataType;

use Grid\GridRow;

use \DateTime;
use \Exception;

/**
 *
 * @author Gospodinow
 */
abstract class AbstractDateTime implements DataTypeInterface
{
    /**
     *
     * @param DateTime $mixed
     * @return int
     * @throws Exception
     */
    public function strtotime($mixed) : int
    {
        if (is_string($mixed)) {
            return strtotime($mixed);
        } elseif (is_int($mixed)) {
            return $mixed;
        } elseif ($mixed instanceof DateTime) {
            return $mixed;
        }
        throw new Exception('strtotime expects sting/int/datetime');
    }

    /**
     *
     * @return int
     */
    protected function time() : int
    {
        return time();
    }

    /**
     *
     * @param type $format
     * @param type $mixed
     * @return string
     */
    public function date($format, $mixed) : string
    {
        return date($format, $this->strtotime($mixed));
    }
}