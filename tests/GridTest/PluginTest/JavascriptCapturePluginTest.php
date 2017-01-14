<?php
namespace GridTest\HydratorTest;

use Grid\Plugin\JavascriptCapturePlugin;
use Grid\Util\JavascriptCapture;
use Grid\Renderer\HtmlRenderer;
use Grid\Grid;

use Grid\Interfaces\JavascriptPluginInterface;
use Grid\Interfaces\JavascriptCaptureInterface;

use PHPUnit\Framework\TestCase;

class JavascriptCapturePluginTest extends TestCase implements JavascriptPluginInterface
{
    public function testPaginationPlugin()
    {
        $grid = new Grid;
        $grid[] = $this;
        $grid[] = new HtmlRenderer;
        
        $html = $grid->render();
        $this->assertTrue(strpos($html, "document.write('Hello');") !== false);
    }

    /**
     *
     * @param JavascriptCaptureInterface $script
     */
    public function addJavascript(JavascriptCaptureInterface $script) : JavascriptCaptureInterface
    {
        $script->captureStart();
        ?>
        <script>
            document.write('Hello');
        </script>
        <?php
        $script->captureEnd();
        return $script;
    }
}
