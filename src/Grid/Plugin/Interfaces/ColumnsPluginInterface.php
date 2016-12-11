<?php

namespace Grid\Plugin\Interfaces;

/**
 * All objects added to the grid with
 * this interface will be able to filter the columns
 *
 * @author Gospodinow
 */
interface ColumnsPluginInterface
{
    public function filterColumns(array $columns) : array;
}