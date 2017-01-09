<?php

namespace Grid\Interfaces;

/**
 * All objects added to the grid with
 * this interface will be able to filter the columns
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface ColumnsPluginInterface
{
    public function filterColumns(array $columns) : array;
}