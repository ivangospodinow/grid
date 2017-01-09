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
    public function canOrder() : bool;

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
     * @param string $sign
     * @param string $value
     */
    public function orWhere(AbstractColumn $column, string $sign, string $value);

    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function andLike(AbstractColumn $column, string $value);

    /**
     *
     * @param AbstractColumn $column
     * @param string $value
     */
    public function orLike(AbstractColumn $column, string $value);

    /**
     *
     * @param AbstractColumn $column
     * @return array
     */
    public function getColumnValues(AbstractColumn $column) : array;

    /**
     * Query start from record
     * @param int $start
     */
    public function setStart(int $start);
    public function getStart() : int;

    /**
     * Query ends to record
     * @param int $end
     */
    public function setEnd(int $end);
    public function getEnd() : int;
}