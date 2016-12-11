<?php

namespace Grid;

interface GridInterface
{
    public function getGrid() : Grid;
    public function setGrid(Grid $grid);
}