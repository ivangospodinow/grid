<?php
namespace GridTest\DataTypeTest;

use Grid\Grid;
use Grid\Column\Column;
use Grid\GridRow;
use Grid\DataType\Image;

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testDate()
    {
        $column = new Column(['name' => 'test']);
        $row = new GridRow([]);
        $row->setGrid(new Grid());
        $filter = new Image;
        $this->assertTrue($filter->filter('test', $column, $row) === 'test');
        $this->assertTrue(strpos($filter->filter('http://192.168.0.1/test.jpg', $column, $row), '<img') !== false);
    }
}