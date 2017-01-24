<?php
namespace Grid\DataType;

use Grid\Row\AbstractRow;
use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Image implements DataTypeInterface
{
    public function filter($value, AbstractColumn $column, AbstractRow $contex)
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
