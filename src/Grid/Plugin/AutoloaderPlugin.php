<?php

namespace Grid\Plugin;

use Grid\Interfaces\ColumnsPluginInterface;
use Grid\Interfaces\RenderPluginInterface;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\ColumnPluginInterface;
use Grid\Interfaces\JavascriptCaptureInterface;
use Grid\Interfaces\ActionHandlerInterface;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\JavascriptCapture;
use Grid\Column\AbstractColumn;

use Grid\Plugin\JavascriptCapturePlugin;
use Grid\Plugin\ExtractorPlugin;
use Grid\Plugin\HeaderPlugin;
use Grid\Plugin\DataTypesPlugin;
use Grid\Plugin\ColumnsOnlyDataPlugin;
use Grid\Plugin\ActionHandlerPlugin;

/**
 * Creating table headers
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class AutoloaderPlugin extends AbstractPlugin implements
    ColumnsPluginInterface,
    GridInterface,
    ColumnPluginInterface,
    RenderPluginInterface
{
    use GridAwareTrait;
    
    protected $autoloadedDataTypesPlugin = false;
    
    public function filterColumns(array $columns) : array
    {
        $this->autoload();
        return $columns;
    }

    public function preRender(string $html) : string
    {
        if (!isset($this->getGrid()[ActionHandlerInterface::class])) {
            $this->getGrid()[] = new ActionHandlerPlugin;
            foreach ($this->getGrid()[ActionHandlerInterface::class] as $plugin) {
                $plugin->preRender($html);
            }
        }
        
        if (!isset($this->getGrid()[JavascriptCaptureInterface::class])) {
            $this->getGrid()[] = new JavascriptCapture;
            $this->getGrid()[] = new JavascriptCapturePlugin;
            foreach ($this->getGrid()[JavascriptCapturePlugin::class] as $plugin) {
                $plugin->preRender($html);
            }
        }
        return $html;
    }

    public function postRender(string $html) : string
    {
        return $html;
    }

    /**
     *
     * @param AbstractColumn $column
     */
    public function filterColumn(AbstractColumn $column) : AbstractColumn
    {
        if (false === $this->autoloadedDataTypesPlugin
        && $column->hasDataType()) {
            $this->autoloadedDataTypesPlugin = true;
            $this->getGrid()[] = new DataTypesPlugin;
        }
        return $column;
    }

    /**
     * Grid essentials
     */
    public function autoload()
    {
        if (!isset($this->getGrid()[ExtractorPlugin::class])) {
            $this->getGrid()[] = new ExtractorPlugin;
        }
        
        if (!isset($this->getGrid()[HeaderPlugin::class])) {
            $this->getGrid()[] = new HeaderPlugin;
        }

        if (!isset($this->getGrid()[ColumnsOnlyDataPlugin::class])) {
            $this->getGrid()[] = new ColumnsOnlyDataPlugin;
        }
    }
}
