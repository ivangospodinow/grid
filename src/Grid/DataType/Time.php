<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Gospodinow
 */
class Time extends AbstractDateTime
{
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        return $this->date(
            $column->getFormat() ? $column->getFormat() : 'H:i:s',
            $value
        );
    }
}