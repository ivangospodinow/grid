<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Row\BodyRow;

/**
 * Uses data type plugins
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
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
            $this->getGrid()->setObjectDi($dataType);
            foreach ($data as $row) {
                if (!$row instanceof BodyRow) {
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
