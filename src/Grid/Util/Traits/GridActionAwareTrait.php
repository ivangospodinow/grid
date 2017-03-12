<?php
namespace Grid\Util\Traits;

use Grid\Grid;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait GridActionAwareTrait
{
    /**
     *
     * @param array $params
     * @param Grid $grid
     * @param array $actionParams
     * @param array $data
     */
    public function addGridAction(array &$params, Grid $grid, array $actionParams, array $data)
    {
        $params['grid'][$grid->getId()]['action'][base64_encode(json_encode($actionParams))] = $data;
    }
}