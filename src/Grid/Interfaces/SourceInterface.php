<?php
namespace Grid\Interfaces;

use Grid\Column\AbstractColumn;

/**
 * Description of SourceInterface
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface SourceInterface
{
    public function getRows();
    public function setRows(array $rows);
    public function getCount() : int;
    public function setCount(int $count);
    public function order();

    /**
     *
     * @param AbstractColumn $column
     * @param string $sign
     * @param string $value
     */
    public function andWhere(AbstractColumn $column, string $sign, string $value);
    
    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function andLike(AbstractColumn $column, string $value);
    
    /**
     *
     * @param AbstractColumn $column
     * @return array
     */
    public function getColumnValues(AbstractColumn $column) : array;

    /**
     * Query start from record
     * @param int $offset
     */
    public function setOffset(int $offset);
    public function getOffset() : int;

    /**
     * Query ends to record
     * @param int $limit
     */
    public function setLimit(int $limit);
    public function getLimit() : int;
}