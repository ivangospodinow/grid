<?php
namespace Grid\DataType;

use Grid\Row\AbstractRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Date extends AbstractDateTime
{
    public function filter($value, AbstractColumn $column, AbstractRow $contex)
    {
        return $this->date(
            $column->getFormat() ? $column->getFormat() : 'Y-m-d',
            $value
        );
    }
}