<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\GridRow;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;

/**
 * This class will get the column value from provided source
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ExtractorPlugin extends AbstractPlugin implements RowPluginInterface, GridInterface
{
    use GridAwareTrait;
    
    /**
     * gets the column value from source
     * 
     * @param GridRow $gridRow
     * @return GridRow
     */
    public function filterRow(GridRow $gridRow) : GridRow
    {
        $columns = $this->getGrid()->getColumns();
        foreach ($columns as $column) {
            $gridRow[$column->getName()] =
            $column->getExtractor($gridRow->getSource())
                   ->extract($gridRow->getSource(), $column->getExtract());
        }
        return $gridRow;
    }
}