<?php

namespace Grid;

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