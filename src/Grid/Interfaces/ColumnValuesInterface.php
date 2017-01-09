<?php

namespace Grid\Interfaces;

/**
 * $values [id => name]
 *
 * @author Gospodinow
 */
interface ColumnValuesInterface
{
    public function filterColumnValues(array $values) : array;
}