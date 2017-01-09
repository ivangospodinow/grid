<?php

namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;

/**
 *
 * @author Gospodinow
 */
class TimeAgo extends AbstractDateTime implements GridInterface
{
    use GridAwareTrait;

    /**
     * @see http://stackoverflow.com/questions/6679010/converting-a-unix-time-stamp-to-twitter-facebook-style
     * @param int $value
     * @param AbstractColumn $column
     * @param GridRow $contex
     * @return type
     */
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        $date       = $this->strtotime($value);
        if ($date === 0) {
            return '';
        }
        $difference = $this->time() - $date;
        if ($difference === 0) {
            $difference = 1;
        }
        $periods    = array(
            'decade' => 315360000,
            'year' => 31536000,
            'month' => 2628000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1
        );
        $retval = '';
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time       = floor($difference / $value);
                $difference %= $value;
                $retval     .= $time . ' ';
                $label       = (($time > 1) ? $key.'s' : $key);
                $retval     .= $this->getGrid()->translate($label);
                break;
            }
        }
        return $retval;
    }
}