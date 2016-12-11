<?php

namespace Grid\Plugin\Interfaces;

/**
 * All objects added to the grid with
 * this interface will be able to filter the final data
 *
 * @author Gospodinow
 */
interface DataPluginInterface
{
    public function filterData(array $data) : array;
}