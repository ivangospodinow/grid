<?php
namespace GridTest\DataTypeTest;

use Grid\Grid;
use Grid\Column\Column;
use Grid\GridRow;
use Grid\DataType\Date;

use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testDate()
    {
        $time = time();
        $column = new Column(['name' => 'test']);
        $row = new GridRow([]);
        $row->setGrid(new Grid());
        $filter = new Date;
        $this->assertTrue($filter->filter(new \DateTime(date('Y-m-d', $time)), $column, $row) === date('Y-m-d', $time));

        $column->setFormat('Ymd');
        $this->assertTrue($filter->filter($time, $column, $row) === date('Ymd', $time));
        $this->assertFalse($filter->filter('fasfasf', $column, $row) === date('Ymd', $time));
        $this->assertTrue($filter->filter(new \DateTime(date('Y-m-d', $time)), $column, $row) === date('Ymd', $time));
    }
}