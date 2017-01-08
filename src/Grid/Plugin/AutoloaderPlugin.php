<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\ColumnsPrePluginInterface;
use Grid\Plugin\Interfaces\DataPrePluginInterface;
use Grid\Plugin\Interfaces\RenderPluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;
use Grid\Plugin\Interfaces\ColumnPluginInterface;
use Grid\Column\AbstractColumn;
use Grid\Renderer\CliRenderer;

use Grid\Util\JavascriptCapture;
use Grid\Interfaces\JavascriptCaptureInterface;

use Grid\Plugin\JavascriptCapturePlugin;
use Grid\Plugin\ExtractorPlugin;
use Grid\Plugin\HeaderPlugin;
use Grid\Plugin\DataTypesPlugin;
use Grid\Plugin\ColumnsOnlyDataPlugin;

/**
 * Creating table headers
 *
 * @author Gospodinow
 */
class AutoloaderPlugin extends AbstractPlugin implements
    ColumnsPrePluginInterface,
    GridInterface,
    ColumnPluginInterface,
    DataPrePluginInterface,
    RenderPluginInterface
{
    use GridAwareTrait;
    
    protected $autoloaded = false;
    protected $autoloadedDataTypesPlugin = false;

    public function preFilterData(array $data) : array
    {
        $this->autoload();
        return $data;
    }

    public function preColumns(array $columns) : array
    {
        $this->autoload();
        return $columns;
    }

    public function preRender(string $html) : string
    {
        if (!isset($this->getGrid()[JavascriptCaptureInterface::class])) {
            $this->getGrid()[] = new JavascriptCapture;
            $this->getGrid()[] = new JavascriptCapturePlugin;
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
        if (true === $this->autoloaded) {
            return;
        }
        $this->autoloaded = true;

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
