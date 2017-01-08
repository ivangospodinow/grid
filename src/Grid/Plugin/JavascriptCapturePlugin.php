<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\RenderPluginInterface;

/**
 * Adds javascript to the render output
 *
 * @author Gospodinow
 */
class JavascriptCapturePlugin extends AbstractPlugin implements RenderPluginInterface
{
    /**
     *
     */
    public function preRender(string $html) : string
    {
        return $html;
    }

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string
    {
        foreach ($this->getGrid()[JavascriptCaptureInterface::class] as $scriptCapture) {

            $script = (string)
            $this->getGrid()->filter(
               JavascriptPluginInterface::class,
               'addJavascript',
               $scriptCapture
            );

            if (empty($script)) {
                continue;
            }

            $html .= sprintf(
                '%s<script>%s%s%s</script>',
                PHP_EOL,
                PHP_EOL,
                $script,
                PHP_EOL
            );
        }
        return $html;
    }
}
