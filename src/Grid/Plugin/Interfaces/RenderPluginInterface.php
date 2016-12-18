<?php

namespace Grid\Plugin\Interfaces;

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
