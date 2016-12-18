<?php

namespace Grid\Plugin;

use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\GridInterface;

/**
 * Uses data type plugins
 *
 * @author Gospodinow
 */
class DataTypesPlugin extends AbstractPlugin implements DataPluginInterface
{
    public function filterData(array $data) : array
    {
        foreach ($this->getGrid()->getColumns() as $column) {
            if (!$column->hasDataType()) {
                continue;
            }

            $dataTypeClass = $column->getDataType();
            $dataType = new $dataTypeClass;
            if ($dataType instanceof GridInterface) {
                $dataType->setGrid($this->getGrid());
            }
            foreach ($data as $row) {
                if (!$row->isBody()) {
                    continue;
                }

                $row[$column->getName()] = $dataType->filter(
                    $row[$column->getName()],
                    $column,
                    $row
                );
            }
        }

        return $data;
    }
}
