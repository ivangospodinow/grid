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
     * @param array $data
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
            $gridRow[$column->getName()] = sprintf(
                '%s%s%s',
                $column->getPreLabel(),
                $column->getLabel(),
                $column->getPostLabel()
            );
        }
        $data[] = $gridRow;
        return $data;
    }
}