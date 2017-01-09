<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\GridRow;

/**
 * This class will get the column value from provided source
 *
 * @author Gospodinow
 */
class ExtractorPlugin extends AbstractPlugin implements RowPluginInterface
{
    /**
     * gets the column value from source
     * 
     * @param GridRow $gridRow
     * @return GridRow
     */
    public function filterRow(GridRow $gridRow) : GridRow
    {
        $columns = $gridRow->getGrid()->getColumns();
        foreach ($columns as $column) {
            $gridRow[$column->getName()] =
            $column->getExtractor($gridRow->getSource())
                   ->extract($gridRow->getSource(), $column->getExtract());
        }
        return $gridRow;
    }
}