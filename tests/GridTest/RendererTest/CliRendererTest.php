<?php
namespace GridTest\HydratorTest;

use Grid\Grid;
use Grid\Renderer\CliRenderer;
use Grid\Source\ArraySource;

use PHPUnit\Framework\TestCase;

class CliRendererTest extends TestCase
{
    public function testHydrator()
    {
        $grid = new Grid;
        $grid[] = new \Grid\Column\Column(['name' => 'test']);
        $grid[] = new \Grid\Plugin\HeaderPlugin();
        $grid[] = new ArraySource(['driver' => [['test' => 'I_AM_HERE']]]);
        $renderer = new CliRenderer;
        $this->assertTrue(is_string($renderer->render($grid)));
        $this->assertTrue(strpos($renderer->render($grid), 'I_AM_HERE') !== false);
    }
}