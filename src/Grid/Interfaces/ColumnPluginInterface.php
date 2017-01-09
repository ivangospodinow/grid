<?php

namespace Grid\Interfaces;

use Grid\Column\AbstractColumn;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface ColumnPluginInterface
{

    /**
     *
     * @param AbstractColumn $column
     */
    public function filterColumn(AbstractColumn $column) : AbstractColumn;
}