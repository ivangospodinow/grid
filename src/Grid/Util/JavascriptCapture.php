<?php

namespace Grid\Util;

use Grid\Interfaces\JavascriptCaptureInterface;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class JavascriptCapture implements JavascriptCaptureInterface
{
    protected $buffer = [];
    
    public function __toString() : string
    {
        return implode(PHP_EOL, $this->buffer);
    }
    
    public function captureStart()
    {
        ob_start();
    }
    
    public function captureEnd()
    {
        $this->buffer[] = ob_get_clean();
    }
}
