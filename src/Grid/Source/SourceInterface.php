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
}