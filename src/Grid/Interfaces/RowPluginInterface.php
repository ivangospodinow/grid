<?php

namespace Grid\Interfaces;

use Grid\Row\AbstractRow;

/**
 * All objects added to the grid with
 * this interface will be able to filter every data row
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface RowPluginInterface
{
    public function filterRow(AbstractRow $row) : AbstractRow;
}