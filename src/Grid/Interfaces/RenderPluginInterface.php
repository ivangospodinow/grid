<?php

namespace Grid\Interfaces;

/**
 * 
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface RenderPluginInterface
{
    /**
     *
     * @param string $html
     */
    public function preRender(string $html) : string;

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string;
}
