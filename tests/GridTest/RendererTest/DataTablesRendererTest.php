<?php
namespace GridTest\HydratorTest;

use Grid\Grid;
use Grid\Renderer\DataTablesRenderer;
use Grid\Source\ArraySource;
use Grid\Plugin\PaginationPlugin;
use Grid\Plugin\ColumnSortablePlugin;

use PHPUnit\Framework\TestCase;

class DataTablesRendererTest extends TestCase
{
    public function testHydrator()
    {
        $grid = new Grid;
        $grid[] = new \Grid\Column\Column(['name' => 'test']);
        $grid[] = new \Grid\Plugin\HeaderPlugin();
        $grid[] = new ArraySource(['driver' => [['test' => 'I_AM_HERE']]]);
        $grid[] = new DataTablesRenderer;
        $grid[] = new PaginationPlugin;
        $grid[] = new ColumnSortablePlugin;
        
        $html = $grid->render();
        $this->assertTrue(is_string($html));
        $this->assertTrue(strpos($html, 'I_AM_HERE') !== false);
        $this->assertTrue(strpos($html, '.DataTable(') !== false);
        $this->assertTrue(strpos($html, '"paging":true') !== false);
        $this->assertTrue(strpos($html, '"ordering":true') !== false);
    }
}