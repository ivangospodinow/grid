<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Gospodinow
 */
class DateTime extends AbstractDateTime
{
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        return $this->date(
            $column->getFormat() ? $column->getFormat() : 'Y-m-d H:i:s',
            $value
        );
    }
}