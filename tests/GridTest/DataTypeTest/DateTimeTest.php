<?php
namespace GridTest\DataTypeTest;

use Grid\Grid;
use Grid\Column\Column;
use Grid\GridRow;
use Grid\DataType\DateTime;

use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testDate()
    {
        $time = time();
        $column = new Column(['name' => 'test']);
        $row = new GridRow([]);
        $row->setGrid(new Grid());
        $filter = new DateTime;
        $this->assertTrue($filter->filter($time, $column, $row) === date('Y-m-d H:i:s', $time));

        $column->setFormat('YmdHis');
        $this->assertTrue($filter->filter($time, $column, $row) === date('YmdHis', $time));
        $this->assertFalse($filter->filter('fasfasf', $column, $row) === date('YmdHis', $time));
        $this->assertTrue($filter->filter(new \DateTime(date('Y-m-d H:i:s', $time)), $column, $row) === date('YmdHis', $time));

        $this->assertTrue($filter->filter($this, $column, $row) === '');
        $this->assertTrue($filter->strtotime(new \DateTime(date('Y-m-d H:i:s', $time))) === $time);
    }
}