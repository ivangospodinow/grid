<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\GridRow;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;

/**
 * Removing all html in column value, used for cli view
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class StripHtmlPlugin extends AbstractPlugin implements RowPluginInterface, GridInterface
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
            $gridRow[$column->getName()] = trim(strip_tags($gridRow[$column->getName()]));
        }

        return $gridRow;
    }
}