<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Row\AbstractRow;

/**
 * Allows grid to have different columns for different profiles
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ColumnsOnlyDataPlugin extends AbstractPlugin implements DataPluginInterface
{
    /**
     * Remove all fields that are not in the profile
     * @param array $data
     * @return array
     */
    public function filterData(array $data) : array
    {
        $names = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            $names[] = $column->getName();
        }
        foreach ($data as $row) {
            $this->filterDataRow($row, $names);
        }
        return $data;
    }

    public function filterDataRow(AbstractRow $row, array $columns)
    {
        foreach ($row as $column => $value) {
            if (!in_array($column, $columns)) {
                unset($row[$column]);
            }
        }
    }
}
