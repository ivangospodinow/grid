<?php
namespace Grid\Interfaces;

use Grid\Grid;

/**
 * Description of RendererInterface
 *
 * @author Gospodinow
 */
interface RendererInterface
{
    public function render(Grid $grid) : string;
}