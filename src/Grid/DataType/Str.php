<?php
namespace Grid\DataType;

use Grid\Row\AbstractRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Str implements DataTypeInterface
{
    public function filter($value, AbstractColumn $column, AbstractRow $contex)
    {
        if (is_object($value)
        && !method_exists($value, '__toString')) {
            return '';
        }
        return (string) $value;
    }
}