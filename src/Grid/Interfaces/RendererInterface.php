<?php
namespace Grid\Interfaces;

use Grid\Grid;

/**
 * Description of RendererInterface
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface RendererInterface
{
    public function render(Grid $grid) : string;
}