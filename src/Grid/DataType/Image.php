<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Gospodinow
 */
class Image implements DataTypeInterface
{
    public function filter($value, AbstractColumn $column, GridRow $contex)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return sprintf(
                '<img src="%s" alt="%s"/>',
                $value,
                $value
            );
        }

        return $value;
    }
}
