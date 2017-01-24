<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\Row\AbstractRow;

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
     * @param AbstractRow $row
     * @return AbstractRow
     */
    public function filterRow(AbstractRow $row) : AbstractRow
    {
        $columns = $this->getGrid()->getColumns();
        foreach ($columns as $column) {
            $row[$column->getName()] = trim(strip_tags($row[$column->getName()]));
        }

        return $row;
    }
}