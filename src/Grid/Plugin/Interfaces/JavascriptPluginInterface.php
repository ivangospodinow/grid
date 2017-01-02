<?php

namespace Grid\Plugin\Interfaces;

use Grid\Interfaces\JavascriptCaptureInterface;

/**
 *
 * @author Gospodinow
 */
interface JavascriptPluginInterface
{
    /**
     *
     * @param JavascriptCaptureInterface $script
     */
    public function addJavascript(JavascriptCaptureInterface $script) : JavascriptCaptureInterface;
}