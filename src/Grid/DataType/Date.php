<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Date extends AbstractDateTime
{
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        return $this->date(
            $column->getFormat() ? $column->getFormat() : 'Y-m-d',
            $value
        );
    }
}