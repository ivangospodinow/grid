<?php
namespace GridTest\DataTypeTest;

use Grid\Grid;
use Grid\Column\Column;
use Grid\GridRow;
use Grid\DataType\Time;

use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    public function testDate()
    {
        $time = time();
        $column = new Column(['name' => 'test']);
        $row = new GridRow([]);
        $filter = new Time;
        $this->assertTrue($filter->filter(new \DateTime(date('Y-m-d H:i:s', $time)), $column, $row) === date('H:i:s', $time));

        $column->setFormat('His');
        $this->assertTrue($filter->filter($time, $column, $row) === date('His', $time));
        $this->assertFalse($filter->filter('fasfasf', $column, $row) === date('His', $time));
        $this->assertTrue($filter->filter(new \DateTime(date('Y-m-d H:i:s', $time)), $column, $row) === date('His', $time));
    }
}