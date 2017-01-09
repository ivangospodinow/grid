<?php
namespace Grid\Util\Traits;

use Grid\Grid;

/**
 * Description of GridAwareTrait
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait GridAwareTrait
{
    /**
     *
     * @var Grid
     */
    protected $grid;
    
    public function getGrid() : Grid
    {
        return $this->grid;
    }

    /**
     *
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
    }
}