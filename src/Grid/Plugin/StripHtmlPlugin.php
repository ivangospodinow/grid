<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Row\AbstractRow;

/**
 * Removing all html in column value, used for cli view
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class StripHtmlPlugin extends AbstractPlugin implements DataPluginInterface
{
    /**
     * @param array $data
     */
    public function filterData(array $data) : array
    {
        foreach ($data as $row) {
            if ($row->isString()) {
                continue;
            }
            foreach ($row as $name => $value) {
                $row[$name] = trim(strip_tags($value));
            }
        }
        return $data;
    }
}