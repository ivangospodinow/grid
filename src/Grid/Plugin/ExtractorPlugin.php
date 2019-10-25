<?php

namespace Grid\Plugin;

use Grid\Interfaces\RowPluginInterface;
use Grid\Row\AbstractRow;

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
     * @param AbstractRow $row
     * @return $row
     */
    public function filterRow(AbstractRow $row) : AbstractRow
    {
        $columns = $this->getGrid()->getColumns();
        foreach ($columns as $column) {
            $value = $column->getExtractor($row->getSource())
                ->extract($row->getSource(), $column->getExtract());
            if ($value !== null || !isset($row[$column->getName()])) {
                $row[$column->getName()] = $value;
            }
        }
        return $row;
    }
}
