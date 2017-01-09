<?php

namespace Grid\Interfaces;

use Grid\Grid;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface GridInterface
{
    /**
     * @return \Grid\Grid
     */
    public function getGrid() : Grid;

    /**
     *
     * @param \Grid\Grid $grid
     */
    public function setGrid(Grid $grid);
}