<?php

namespace Grid\Interfaces;

/**
 * $values [id => name]
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface ColumnValuesInterface
{
    public function filterColumnValues(array $values) : array;
}