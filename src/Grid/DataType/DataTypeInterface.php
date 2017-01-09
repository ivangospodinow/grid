<?php
namespace Grid\DataType;

use Grid\GridRow;
use Grid\Column\AbstractColumn;

/**
 * Description of DataTypeInterface
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface DataTypeInterface
{
    public function filter($value, AbstractColumn $column, GridRow $contex);
}