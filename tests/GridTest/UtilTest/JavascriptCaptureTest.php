<?php
namespace GridTest\UtilTest;

use Grid\Util\JavascriptCapture;

use PHPUnit\Framework\TestCase;

class JavascriptCaptureTest extends TestCase
{
    public function testJavascriptCapture()
    {
        $script = new JavascriptCapture;
        $script->captureStart();
        echo '123';
        $script->captureEnd();

        $this->assertTrue(strpos((string) $script, '123') !== false);
    }
}