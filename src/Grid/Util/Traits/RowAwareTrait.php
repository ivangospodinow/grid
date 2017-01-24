<?php
namespace Grid\Util\Traits;

/**
 * Common rows operations
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait RowAwareTrait
{
    /**
     *
     * @param array $rows
     * @param string $class
     * @param int $index
     * @return array
     */
    public function getIndexRows(array $rows, string $class, int $index) : array
    {
        $found = [];
        foreach ($rows as $row) {
            if ($row instanceof $class
            && $row->getIndex() === $index) {
                $found[] = $row;
            }
        }
        return $found;
    }
}
