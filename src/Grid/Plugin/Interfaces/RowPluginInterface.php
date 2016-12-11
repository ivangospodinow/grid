<?php

namespace Grid\Plugin\Interfaces;

use Grid\GridRow;

/**
 * All objects added to the grid with
 * this interface will be able to filter every data row
 *
 * @author Gospodinow
 */
interface RowPluginInterface
{
    public function filterRow(GridRow $gridRow) : GridRow;
}