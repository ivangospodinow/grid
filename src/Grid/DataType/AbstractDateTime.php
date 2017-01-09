<?php
namespace Grid\DataType;

use \DateTime;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
abstract class AbstractDateTime implements DataTypeInterface
{
    /**
     *
     * @param DateTime $mixed
     * @return int
     */
    public function strtotime($mixed) : int
    {
        if (is_string($mixed)) {
            return strtotime($mixed);
        } elseif (is_int($mixed) || is_numeric($mixed)) {
            return (int) $mixed;
        } elseif ($mixed instanceof DateTime) {
            return $mixed->getTimestamp();
        }
        return 0;
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
        $time = $this->strtotime($mixed);
        if ($time === 0) {
            return '';
        }
        return date($format, $time);
    }
}