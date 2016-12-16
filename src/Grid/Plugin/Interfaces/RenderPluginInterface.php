<?php

namespace Grid\Plugin\Interfaces;

use Grid\Grid;

/**
 * 
 *
 * @author Gospodinow
 */
interface RenderPluginInterface
{
    /**
     * 
     */
    public function preRender();

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string;
}
