<?php

namespace Grid\Plugin;

use Grid\Interfaces\SourceInterface;
use Grid\Interfaces\ActionHandleInterface;
use Grid\Interfaces\RenderPluginInterface;
use Grid\Interfaces\DataPluginInterface;
use Grid\Interfaces\JavascriptPluginInterface;
use Grid\Interfaces\JavascriptCaptureInterface;
use Grid\Interfaces\RendererInterface;
use Grid\Interfaces\GridInterface;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\GridActionAwareTrait;

use Grid\Renderer\AjaxRenderer;

use Grid\Row\BodyRow;

/**
 * Grid ajax handling
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class AjaxPlugin extends AbstractPlugin implements
    ActionHandleInterface,
    RenderPluginInterface,
    GridInterface,
    DataPluginInterface,
    JavascriptPluginInterface
{
    use GridAwareTrait, LinkCreatorAwareTrait, GridActionAwareTrait;

    const ACTION = 'ajax';

    protected $loadingMessage = 'Loading...';
    protected $actionActive   = false;
    protected $callback       = '';
    
    public function handleAction(array $params) : array
    {
        if (isset($params[self::ACTION])) {
            unset($this->getGrid()[RendererInterface::class]);
            $this->getGrid()[] = new AjaxRenderer;
            $this->getLinkCreator()->setParams($params[self::ACTION]['params']);
            $this->callback = $params[self::ACTION]['action']['callback'];
            $this->actionActive = true;
        }
        return $params;
    }
    
    /**
     * 
     * @param array $data
     */
    public function filterData(array $data) : array
    {
        if (!$this->actionActive) {
            $data[] = new BodyRow($this->getGrid()->translate($this->loadingMessage));
        }
        return $data;
    }

    /**
     *
     * @param string $html
     */
    public function preRender(string $html) : string
    {
        if (!$this->actionActive) {
            unset($this->getGrid()[SourceInterface::class]);
        } else {
            $json = '';
            foreach ($this->getGrid()[RendererInterface::class] as $renderer) {
                $json .= $renderer->render($this->getGrid());
            }
            echo $this->callback . '(' . $json . ')';
            exit;
        }
        return $html;
    }

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string
    {
        return $html;
    }

    /**
     *
     * @param JavascriptCaptureInterface $script
     */
    public function addJavascript(JavascriptCaptureInterface $script) : JavascriptCaptureInterface
    {
        if ($this->actionActive) {
            return $script;
        }

        $callback = uniqid('gridCallback');
        
        $params = $this->getLinkCreator()->getParams();
        $this->addGridAction(
            $params,
            $this->getGrid(),
            [
                'action' => self::ACTION,
                'callback' => $callback
            ],
            ['1']
        );
        $script->captureStart();
        ?>
        //<script>
            (function (window, document, callback, query, basePath) {
                
                window[callback] = function (data)
                {
                    if (typeof data !== 'object') {
                        console.error('invalid data received, json expected');
                        return;
                    }
                    var grid  = document.getElementById(data['id']); ;
                    grid.getElementsByTagName('tbody')[0].innerHTML = data['tbody'];
                    grid.getElementsByTagName('tfoot')[0].innerHTML = data['tfoot'];
                };

                var script = document.createElement("script");
                script.src = basePath + '?' + query;
                script.async = true;
                document.head.appendChild(script);

            })(
                window,
                document,
                '<?php echo $callback;?>',
                '<?php echo http_build_query($params); ?>',
                '<?php echo $this->getLinkCreator()->getPageBasePath(); ?>'
            );
        //</script>
        <?php
        $script->captureEnd();
        return $script;
    }
}