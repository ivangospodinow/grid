<?php
namespace Grid\DataType;

use Grid\Row\AbstractRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Time extends AbstractDateTime
{
    public function filter($value, AbstractColumn $column, AbstractRow $contex)
    {
        return $this->date(
            $column->getFormat() ? $column->getFormat() : 'H:i:s',
            $value
        );
    }
}