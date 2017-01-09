<?php

namespace Grid\Interfaces;

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
    public function preRender(string $html) : string;

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string;
}
