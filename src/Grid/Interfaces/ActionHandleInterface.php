<?php

namespace Grid\Interfaces;

use Grid\Grid;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface ActionHandleInterface
{
    /**
     * @return []
     */
    public function handle(array $params) : array;
}
