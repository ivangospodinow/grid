<?php
namespace Grid\Source;

/**
 * Description of SourceInterface
 *
 * @author Gospodinow
 */
interface SourceInterface
{
    public function getRows();
    public function setRows(array $rows);
    public function getCount() : int;
    public function setCount(int $count);

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