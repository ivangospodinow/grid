<?php
namespace Grid\DataType;

use Grid\Row\AbstractRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Integer implements DataTypeInterface
{
    public function filter($value, AbstractColumn $column, AbstractRow $contex)
    {
        return (int) $value;
    }
}