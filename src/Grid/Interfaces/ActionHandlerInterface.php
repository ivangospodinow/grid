<?php

namespace Grid\Interfaces;

use Grid\Grid;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface ActionHandlerInterface
{
    /**
     * @return []
     */
    public function handleAction(Grid $grid) : Grid;
}
