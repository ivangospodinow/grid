<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\GridRow;

/**
 * Removing all html in column value, used for cli view
 *
 * @author Gospodinow
 */
class StripHtmlPlugin extends AbstractPlugin implements RowPluginInterface
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
            $gridRow[$column->getName()] = trim(strip_tags($gridRow[$column->getName()]));
        }

        return $gridRow;
    }
}