<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\ColumnsPrePluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use Grid\Plugin\ExtractorPlugin;
use Grid\Plugin\HeaderPlugin;

/**
 * Creating table headers
 *
 * @author Gospodinow
 */
class AutoloaderPlugin extends AbstractPlugin implements ColumnsPrePluginInterface, GridInterface
{
    use GridAwareTrait;
    
    protected $autoloaded = false;
    
    public function preColumns(array $columns) : array
    {
        $this->autoload();
        return $columns;
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
