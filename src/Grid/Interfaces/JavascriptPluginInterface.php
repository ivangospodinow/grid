<?php

namespace Grid\Interfaces;

use Grid\Interfaces\JavascriptCaptureInterface;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface JavascriptPluginInterface
{
    /**
     *
     * @param JavascriptCaptureInterface $script
     */
    public function addJavascript(JavascriptCaptureInterface $script) : JavascriptCaptureInterface;
}
