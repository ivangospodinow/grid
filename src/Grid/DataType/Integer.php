<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Gospodinow
 */
class Integer implements DataTypeInterface
{
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        return (int) $value;
    }
}