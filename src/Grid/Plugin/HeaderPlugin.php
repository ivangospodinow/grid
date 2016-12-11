<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\GridRow;

/**
 * Creating table headers
 *
 * @author Gospodinow
 */
class HeaderPlugin extends AbstractPlugin implements DataPluginInterface
{
    /**
     * gets the column value from source
     * 
     * @param GridRow $gridRow
     * @return GridRow
     */
    public function filterData(array $data) : array
    {
        $gridRow = new GridRow(
            [],
            $this->getGrid(),
            GridRow::POSITION_HEAD
        );
        foreach ($this->getGrid()->getColumns() as $column) {
            $gridRow[$column->getName()] = $column->getLabel();
        }
        $data[] = $gridRow;
        return $data;
    }
}