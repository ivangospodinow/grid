<?php

namespace Grid\Interfaces;

use Grid\Column\AbstractColumn;

/**
 *
 * @author Gospodinow
 */
interface ColumnPluginInterface
{

    /**
     *
     * @param AbstractColumn $column
     */
    public function filterColumn(AbstractColumn $column) : AbstractColumn;
}