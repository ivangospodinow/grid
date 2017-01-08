<?php
namespace GridTest\DataTypeTest;

use Grid\Grid;
use Grid\Column\Column;
use Grid\GridRow;
use Grid\DataType\Str;

use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function __toString()
    {
        return '';
    }

    public function testDate()
    {
        $column = new Column(['name' => 'test']);
        $row = new GridRow([]);
        $row->setGrid(new Grid());
        $filter = new Str;
        $this->assertTrue(is_string($filter->filter('test', $column, $row)));
        $this->assertTrue(is_string($filter->filter(2.42, $column, $row)));
        $this->assertTrue(is_string($filter->filter($this, $column, $row)));
        $this->assertTrue(is_string($filter->filter(new \stdClass, $column, $row)));
    }
}