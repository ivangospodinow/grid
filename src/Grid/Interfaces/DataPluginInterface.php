<?php

namespace Grid\Interfaces;

/**
 * All objects added to the grid with
 * this interface will be able to filter the final data
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface DataPluginInterface
{
    public function filterData(array $data) : array;
}