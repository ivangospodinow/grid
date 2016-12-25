<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\ColumnsPrePluginInterface;
use Grid\Plugin\Interfaces\DataPrePluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;
use Grid\Plugin\Interfaces\ColumnPluginInterface;
use Grid\Column\AbstractColumn;

use Grid\Plugin\ExtractorPlugin;
use Grid\Plugin\HeaderPlugin;
use Grid\Plugin\DataTypesPlugin;

/**
 * Creating table headers
 *
 * @author Gospodinow
 */
class AutoloaderPlugin extends AbstractPlugin implements
    ColumnsPrePluginInterface,
    GridInterface,
    ColumnPluginInterface,
    DataPrePluginInterface
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

        if (!count($this->getGrid()->getObjects(ExtractorPlugin::class))) {
            $this->getGrid()[] = new ExtractorPlugin;
        }
        
        if (!count($this->getGrid()->getObjects(HeaderPlugin::class))) {
            $this->getGrid()[] = new HeaderPlugin;
        }
    }
}
